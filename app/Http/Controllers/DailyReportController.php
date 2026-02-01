<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\DailyReport;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    // フラッシュは共通ヘルパで統一。
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $user = $request->user();

        $allowedStatus = ['all', 'draft', 'submitted', 'approved', 'rejected'];

        $status = $request->query('status'); // null / all / draft ...
        if ($status !== null && !in_array($status, $allowedStatus, true)) {
            abort(404);
        }

        // 承認者は「未処理（submitted）」をデフォルト表示にする
        if ($user->canApprove() && $status === null) {
            $status = 'submitted';
        }

        // --- ソート（承認者のみ拡張） ---
        $allowedSort = $user->canApprove()
            ? ['report_date', 'user_name', 'total_minutes']
            : ['report_date'];

        $sort = $request->query('sort', 'report_date');
        $dir  = $request->query('dir', 'desc');

        if (!in_array($sort, $allowedSort, true)) abort(404);
        if (!in_array($dir, ['asc', 'desc'], true)) abort(404);

        // --- 警告フィルタ（承認者 + submitted のときだけ有効） ---
        $warn = $request->query('warn', 'all'); // all | warnings
        if (!in_array($warn, ['all', 'warnings'], true)) abort(404);
        if (!($user->canApprove() && $status === 'submitted')) {
            // submitted以外ではwarnは意味がないので常にall扱い
            $warn = 'all';
        }

        $warnSql = DailyReport::warningMinutesSql();
        $warnLimit = DailyReport::WARN_LIMIT_MINUTES;

        $query = DailyReport::query()
            ->with('user')
            ->withSum('timeEntries as total_minutes', 'minutes')
            ->visibleTo($user)
            ->statusFilter($status)
            ->warningsOnly($warn === 'warnings', $warnSql, $warnLimit)
            ->sorted($sort, $dir);

        $reports = $query
            ->paginate(15)
            ->withQueryString();

        // 件数（承認者は全体、一般は自分の分）
        $countBase = DailyReport::query()->visibleTo($user);

        $counts = (clone $countBase)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $totalCount = (clone $countBase)->count();

        // 承認待ちの警告件数（承認者のみ表示用）
        $warningCount = 0;
        if ($user->canApprove()) {
            $warningCount = DailyReport::query()
                ->where('status', 'submitted')
                ->warningsOnly(true, $warnSql, $warnLimit)
                ->count();
        }

        return view('reports.index', compact(
            'reports',
            'status',
            'counts',
            'totalCount',
            'sort',
            'dir',
            'warn',
            'warningCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = \App\Models\Project::query()
            ->orderBy('name')
            ->get();

        return view('reports.create', compact('projects'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDailyReportRequest $request)
    {
        $data = $request->validated();

        $report = new \App\Models\DailyReport();
        $report->report_date = $data['report_date'];
        $report->memo = $data['memo'] ?? null;
        $report->status = 'draft';
        $report->user()->associate($request->user());
        $report->save();

        return $this->redirectRouteWithSuccess(
            'reports.edit',
            $report,
            __('flash.created', ['item' => __('models.daily_report')])
        );
    }



    /**
     * Display the specified resource.
     */
    public function show(\Illuminate\Http\Request $request, \App\Models\DailyReport $report)
    {
        $this->authorize('view', $report);

        $report->load(['user', 'timeEntries.project', 'approver', 'statusLogs.actor']);

        $prevSubmitted = null;
        $nextSubmitted = null;

        if ($request->user()->can('approve', $report)) {
            $actorId = $request->user()->id;

            $prevSubmitted = \App\Models\DailyReport::query()
                ->where('status', 'submitted')
                ->where('user_id', '!=', $actorId)
                ->where(function ($q) use ($report) {
                    $q->where('report_date', '>', $report->report_date)
                    ->orWhere(function ($q2) use ($report) {
                        $q2->where('report_date', $report->report_date)
                            ->where('id', '>', $report->id);
                    });
                })
                ->orderBy('report_date')
                ->orderBy('id')
                ->first();

            $nextSubmitted = \App\Models\DailyReport::query()
                ->where('status', 'submitted')
                ->where('user_id', '!=', $actorId)
                ->where(function ($q) use ($report) {
                    $q->where('report_date', '<', $report->report_date)
                    ->orWhere(function ($q2) use ($report) {
                        $q2->where('report_date', $report->report_date)
                            ->where('id', '<', $report->id);
                    });
                })
                ->orderByDesc('report_date')
                ->orderByDesc('id')
                ->first();
        }

        return view('reports.show', compact('report', 'prevSubmitted', 'nextSubmitted'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, DailyReport $report)
    {
        // そもそも見れない日報なら隠す（情報漏えい防止）
        if (! $request->user()->can('view', $report)) {
            abort(404);
        }

        // 見れるが編集できない（= 状態による）場合は案内して詳細へ戻す
        if (! $request->user()->can('update', $report)) {
            $msg = $this->editDeniedMessage($report);

            return $this->redirectRouteWithError('reports.show', $report, $msg);
        }

        $report->load(['timeEntries.project']);

        // ✅ projects は全体共有：user_id でも status でも絞らない
        $projects = \App\Models\Project::query()
            ->orderBy('name')
            ->get();

        return view('reports.edit', compact('report', 'projects'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDailyReportRequest $request, DailyReport $report)
    {
        if (! $request->user()->can('view', $report)) {
            abort(404);
        }
        if (! $request->user()->can('update', $report)) {
            $msg = $this->editDeniedMessage($report);

            return $this->redirectRouteWithError('reports.show', $report, $msg);
        }

        // $this->authorize('update', $report);

        $data = $request->validated();

        $report->memo = $data['memo'] ?? null;
        $report->save();

        return redirect()->route('reports.show', $report);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, DailyReport $report)
    {
        $this->authorize('delete', $report);

        $report->delete();

        return redirect()->route('reports.index');
    }

    /**
     * 日報の提出
     *
     * @param DailyReport $report 日報
     */
    public function submit(\Illuminate\Http\Request $request, \App\Models\DailyReport $report)
    {
        $this->authorize('update', $report);

        if (!in_array($report->status, ['draft', 'rejected'], true)) {
            return $this->redirectRouteWithError(
                'reports.show',
                $report,
                __('reports.errors.submit_only_draft_or_rejected')
            );
        }

        $from = $report->status;

        $report->status = 'submitted';
        $report->submitted_at = now();
        $report->save();

        $totalMinutes = (int) $report->timeEntries()->sum('minutes');

        $this->logStatusChange(
            $report,
            $request->user()->id,
            'submitted',
            $from,
            'submitted',
            null,
            ['total_minutes' => $totalMinutes]
        );

        return $this->redirectRouteWithSuccess(
            'reports.show',
            $report,
            __('flash.submitted', ['item' => __('models.daily_report')])
        );
    }

    public function approve(\Illuminate\Http\Request $request, \App\Models\DailyReport $report)
    {
        // 自分の日報は承認不可（メッセージで戻す）
        if ((int) $report->user_id === (int) $request->user()->id) {
            return $this->redirectRouteWithError(
                'reports.show',
                $report,
                __('reports.errors.approve_own')
            );
        }

        $this->authorize('approve', $report);

        if ($report->status !== 'submitted') {
            return $this->redirectRouteWithError(
                'reports.show',
                $report,
                __('reports.errors.approve_only_submitted')
            );
        }

        $totalMinutes = (int) $report->timeEntries()->sum('minutes');

        if ($totalMinutes <= 0) {
            return $this->redirectWithFlash(
                route('reports.show', $report) . '#approval-panel',
                $this->flashError(__('reports.errors.approve_zero_minutes'))
            );
        }

        if ($totalMinutes > 24 * 60) {
            return $this->redirectWithFlash(
                route('reports.show', $report) . '#approval-panel',
                $this->flashError(__('reports.errors.approve_over_24h'))
            );
        }

        $from = $report->status;

        $report->status = 'approved';
        $report->rejection_reason = null;
        $report->approved_at = now();
        $report->approved_by = $request->user()->id;
        $report->save();

        $this->logStatusChange(
            $report,
            $request->user()->id,
            'approved',
            $from,
            'approved',
            null,
            ['total_minutes' => $totalMinutes]
        );

        return $this->redirectToNextSubmitted(
            $report,
            $request->user()->id,
            __('flash.approved', ['item' => __('models.daily_report')])
        );
    }

    public function reject(\Illuminate\Http\Request $request, \App\Models\DailyReport $report)
    {
        if ((int) $report->user_id === (int) $request->user()->id) {
            return $this->redirectRouteWithError(
                'reports.show',
                $report,
                __('reports.errors.reject_own')
            );
        }

        $this->authorize('approve', $report);

        if ($report->status !== 'submitted') {
            return $this->redirectRouteWithError(
                'reports.show',
                $report,
                __('reports.errors.reject_only_submitted')
            );
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $from = $report->status;

        $report->status = 'rejected';
        $report->rejection_reason = $data['rejection_reason'];
        $report->approved_at = now();
        $report->approved_by = $request->user()->id;
        $report->save();

        $totalMinutes = (int) $report->timeEntries()->sum('minutes');

        $this->logStatusChange(
            $report,
            $request->user()->id,
            'rejected',
            $from,
            'rejected',
            $data['rejection_reason'],
            ['total_minutes' => $totalMinutes]
        );

        return $this->redirectToNextSubmitted(
            $report,
            $request->user()->id,
            __('flash.rejected', ['item' => __('models.daily_report')])
        );
    }


    private function editDeniedMessage(DailyReport $report): string
    {
        return match ($report->status) {
            'submitted' => __('reports.errors.cannot_edit_submitted'),
            'approved' => __('reports.errors.cannot_edit_approved'),
            default => __('reports.errors.cannot_edit'),
        };
    }

    private function redirectToNextSubmitted(\App\Models\DailyReport $from, int $actorId, string $message)
    {
        $base = \App\Models\DailyReport::query()
            ->where('status', 'submitted')
            ->where('user_id', '!=', $actorId);

        // 「今の順番の次」＝より古い（desc順で後ろ）
        $next = (clone $base)
            ->where(function ($q) use ($from) {
                $q->where('report_date', '<', $from->report_date)
                ->orWhere(function ($q2) use ($from) {
                    $q2->where('report_date', $from->report_date)
                        ->where('id', '<', $from->id);
                });
            })
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->first();

        // もし次が無ければ、残っている最新へ（ぐるっと回す）
        if (! $next) {
            $next = (clone $base)
                ->orderByDesc('report_date')
                ->orderByDesc('id')
                ->first();
        }

        if ($next) {
            return $this->redirectRouteWithSuccess(
                'reports.show',
                $next,
                $message . __('reports.flash.moved_to_next_suffix')
            );
        }

        return $this->redirectRouteWithSuccess(
            'reports.index',
            ['status' => 'submitted'],
            $message . __('reports.flash.no_pending_suffix')
        );
    }

    private function logStatusChange(
        \App\Models\DailyReport $report,
        int $actorId,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        ?string $reason = null,
        array $meta = []
    ): void {
        \App\Models\DailyReportStatusLog::create([
            'daily_report_id' => $report->id,
            'actor_id' => $actorId,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'reason' => $reason,
            'meta' => $meta ?: null,
        ]);
    }

}
