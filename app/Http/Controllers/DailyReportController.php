<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Project;
use App\Models\DailyReport;
use \App\Models\User;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
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

        $warnSql = "COALESCE((SELECT SUM(minutes) FROM time_entries WHERE time_entries.daily_report_id = daily_reports.id), 0)";
        $warnLimit = 24 * 60;

        $query = \App\Models\DailyReport::query()
            ->with('user')
            ->withSum('timeEntries as total_minutes', 'minutes');

        // 承認者でない場合は自分の分だけ
        if (! $user->canApprove()) {
            $query->where('user_id', $user->id);
        }

        // ステータス絞り込み
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // 警告のみ絞り込み（submitted一覧でだけ）
        if ($warn === 'warnings') {
            $query->whereRaw("{$warnSql} = 0 OR {$warnSql} > ?", [$warnLimit]);
        }

        // ソート適用（同値のときの安定ソートも付ける）
        if ($sort === 'report_date') {
            $query->orderBy('report_date', $dir)->orderBy('id', $dir);
        } elseif ($sort === 'total_minutes') {
            $query->orderByRaw('COALESCE(total_minutes, 0) ' . $dir)
                ->orderBy('report_date', 'desc')
                ->orderBy('id', 'desc');
        } elseif ($sort === 'user_name') {
            $query->orderBy(
                    \App\Models\User::select('name')
                        ->whereColumn('users.id', 'daily_reports.user_id'),
                    $dir
                )
                ->orderBy('report_date', 'desc')
                ->orderBy('id', 'desc');
        }

        $reports = $query
            ->paginate(15)
            ->withQueryString();

        // 件数（承認者は全体、一般は自分の分）
        $countBase = \App\Models\DailyReport::query();
        if (! $user->canApprove()) {
            $countBase->where('user_id', $user->id);
        }

        $counts = (clone $countBase)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $totalCount = (clone $countBase)->count();

        // 承認待ちの警告件数（承認者のみ表示用）
        $warningCount = 0;
        if ($user->canApprove()) {
            $warningCount = \App\Models\DailyReport::query()
                ->where('status', 'submitted')
                ->whereRaw("{$warnSql} = 0 OR {$warnSql} > ?", [$warnLimit])
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
    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'report_date' => [
                'required',
                'date',
                \Illuminate\Validation\Rule::unique('daily_reports', 'report_date')
                    ->where(fn ($q) => $q->where('user_id', $request->user()->id)),
            ],
            'memo' => ['nullable', 'string', 'max:2000'],
        ], [
            'report_date.unique' => 'この日付の日報は既に作成されています。',
        ]);

        $report = new \App\Models\DailyReport();
        $report->report_date = $data['report_date'];
        $report->memo = $data['memo'] ?? null;
        $report->status = 'draft';
        $report->user()->associate($request->user());
        $report->save();

        return redirect()
            ->route('reports.edit', $report)
            ->with('success', '日報を作成しました。');
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
            $msg = match ($report->status) {
                'submitted' => 'この日報は提出済みのため編集できません。',
                'approved'  => 'この日報は承認済みのため編集できません。',
                default     => 'この日報は編集できません。',
            };

            return redirect()
                ->route('reports.show', $report)
                ->with('error', $msg);
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
    public function update(Request $request, DailyReport $report)
    {
        if (! $request->user()->can('view', $report)) {
            abort(404);
        }
        if (! $request->user()->can('update', $report)) {
            $msg = match ($report->status) {
                'submitted' => 'この日報は提出済みのため編集できません。',
                'approved'  => 'この日報は承認済みのため編集できません。',
                default     => 'この日報は編集できません。',
            };

            return redirect()
                ->route('reports.show', $report)
                ->with('error', $msg);
        }

        // $this->authorize('update', $report);

        $data = $this->validatedForUpdate($request);

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
     * @param DailyReport $dailyReport 日報
     */
    public function submit(\Illuminate\Http\Request $request, \App\Models\DailyReport $dailyReport)
    {
        $this->authorize('update', $dailyReport);

        if (!in_array($dailyReport->status, ['draft', 'rejected'], true)) {
            return redirect()
                ->route('reports.show', $dailyReport)
                ->with('error', '下書き/差戻しの日報のみ提出できます。');
        }

        $from = $dailyReport->status;

        $dailyReport->status = 'submitted';
        $dailyReport->submitted_at = now();
        $dailyReport->save();

        $totalMinutes = (int) $dailyReport->timeEntries()->sum('minutes');

        $this->logStatusChange(
            $dailyReport,
            $request->user()->id,
            'submitted',
            $from,
            'submitted',
            null,
            ['total_minutes' => $totalMinutes]
        );

        return redirect()
            ->route('reports.show', $dailyReport)
            ->with('success', '日報を提出しました。');
    }

    public function approve(\Illuminate\Http\Request $request, \App\Models\DailyReport $dailyReport)
    {
        // 自分の日報は承認不可（メッセージで戻す）
        if ((int) $dailyReport->user_id === (int) $request->user()->id) {
            return redirect()
                ->route('reports.show', $dailyReport)
                ->with('error', '自分の日報は承認できません。');
        }

        $this->authorize('approve', $dailyReport);

        if ($dailyReport->status !== 'submitted') {
            return redirect()
                ->route('reports.show', $dailyReport)
                ->with('error', '提出済みの日報のみ承認できます。');
        }

        $totalMinutes = (int) $dailyReport->timeEntries()->sum('minutes');

        if ($totalMinutes <= 0) {
            return redirect()
                ->to(route('reports.show', $dailyReport) . '#approval-panel')
                ->with('error', '工数が0分のため承認できません。差戻ししてください。');
        }

        if ($totalMinutes > 24 * 60) {
            return redirect()
                ->to(route('reports.show', $dailyReport) . '#approval-panel')
                ->with('error', '合計工数が24時間を超えているため承認できません。差戻ししてください。');
        }

        $from = $dailyReport->status;

        $dailyReport->status = 'approved';
        $dailyReport->rejection_reason = null;
        $dailyReport->approved_at = now();
        $dailyReport->approved_by = $request->user()->id;
        $dailyReport->save();

        $this->logStatusChange(
            $dailyReport,
            $request->user()->id,
            'approved',
            $from,
            'approved',
            null,
            ['total_minutes' => $totalMinutes]
        );

        return $this->redirectToNextSubmitted($dailyReport, $request->user()->id, '日報を承認しました。');
    }

    public function reject(\Illuminate\Http\Request $request, \App\Models\DailyReport $dailyReport)
    {
        if ((int) $dailyReport->user_id === (int) $request->user()->id) {
            return redirect()
                ->route('reports.show', $dailyReport)
                ->with('error', '自分の日報は差戻しできません。');
        }

        $this->authorize('approve', $dailyReport);

        if ($dailyReport->status !== 'submitted') {
            return redirect()
                ->route('reports.show', $dailyReport)
                ->with('error', '提出済みの日報のみ差戻しできます。');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $from = $dailyReport->status;

        $dailyReport->status = 'rejected';
        $dailyReport->rejection_reason = $data['rejection_reason'];
        $dailyReport->approved_at = now();
        $dailyReport->approved_by = $request->user()->id;
        $dailyReport->save();

        $totalMinutes = (int) $dailyReport->timeEntries()->sum('minutes');

        $this->logStatusChange(
            $dailyReport,
            $request->user()->id,
            'rejected',
            $from,
            'rejected',
            $data['rejection_reason'],
            ['total_minutes' => $totalMinutes]
        );

        return $this->redirectToNextSubmitted($dailyReport, $request->user()->id, '日報を差戻ししました。');
    }


    private function validatedForCreate(Request $request): array
    {
        return $request->validate([
            'report_date' => ['required', 'date'],
            'memo' => ['nullable', 'string'],
        ]);
    }

    private function validatedForUpdate(Request $request): array
    {
        return $request->validate([
            'memo' => ['nullable', 'string'],
        ]);
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
            return redirect()
                ->route('reports.show', $next)
                ->with('success', $message . '（次の承認待ちへ移動しました）');
        }

        return redirect()
            ->route('reports.index', ['status' => 'submitted'])
            ->with('success', $message . '（承認待ちはもうありません）');
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
