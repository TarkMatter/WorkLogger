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
    public function index(Request $request)
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

        if (!in_array($sort, $allowedSort, true)) {
            abort(404);
        }
        if (!in_array($dir, ['asc', 'desc'], true)) {
            abort(404);
        }

        $query = DailyReport::query()
            ->with('user')
            // 一覧用：合計工数をサブクエリで取得（N+1回避）
            ->withSum('timeEntries as total_minutes', 'minutes');

        if (! $user->canApprove()) {
            $query->where('user_id', $user->id);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // ソート適用（同値のときの安定ソートも付ける）
        if ($sort === 'report_date') {
            $query->orderBy('report_date', $dir)
                ->orderBy('id', $dir);
        } elseif ($sort === 'total_minutes') {
            // null対策：COALESCE
            $query->orderByRaw('COALESCE(total_minutes, 0) ' . $dir)
                ->orderBy('report_date', 'desc')
                ->orderBy('id', 'desc');
        } elseif ($sort === 'user_name') {
            // joinなしでユーザー名でソート（サブクエリ）
            $query->orderBy(
                    User::select('name')
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

        return view('reports.index', compact('reports', 'status', 'counts', 'totalCount', 'sort', 'dir'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedForCreate($request);
        $user = $request->user();

        $existing = DailyReport::query()
            ->where('user_id', $user->id)
            ->whereDate('report_date', $data['report_date'])
            ->first();

        if ($existing) {
            return redirect()->route('reports.edit', $existing);
        }

        $report = new DailyReport();
        $report->report_date = $data['report_date'];
        $report->status = 'draft';
        $report->memo = $data['memo'] ?? null;
        $report->user()->associate($user);
        $report->save();

        return redirect()->route('reports.edit', $report);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, DailyReport $report)
    {
        $this->authorize('view', $report);

        $report->load(['user', 'timeEntries.project']);

        $prevSubmitted = null; // より新しい（キュー上で前）
        $nextSubmitted = null; // より古い（キュー上で次）

        if ($request->user()->canApprove() && $report->status === 'submitted') {
            // 前（newer）：report_date が大きい、または同日で id が大きいもののうち、直近
            $prevSubmitted = \App\Models\DailyReport::query()
                ->where('status', 'submitted')
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

            // 次（older）：report_date が小さい、または同日で id が小さいもののうち、直近
            $nextSubmitted = \App\Models\DailyReport::query()
                ->where('status', 'submitted')
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
        // そもそも見れない日報なら隠す（情報漏えい防止
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

        $projects = \App\Models\Project::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
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
    public function submit(Request $request, DailyReport $dailyReport)
    {
        $this->authorize('submit', $dailyReport);

        // ついでに「工数0で提出」を防ぐ（不要ならこのifを消してOK）
        $totalMinutes = $dailyReport->timeEntries()->sum('minutes');
        if ($totalMinutes <= 0) {
            return back()->withErrors([
                'minutes' => '工数が0分のため提出できません。工数を追加してください。',
            ]);
        }

        // 1日24h超え防止（これも不要なら消してOK）
        if ($totalMinutes > 24 * 60) {
            return back()->withErrors([
                'minutes' => '合計工数が24時間を超えています。見直してください。',
            ]);
        }

        $dailyReport->status = 'submitted';
        $dailyReport->rejection_reason = null;
        $dailyReport->submitted_at = now();
        $dailyReport->approved_at = null;
        $dailyReport->approved_by = null;
        $dailyReport->save();

        return redirect()
            ->route('reports.show', $dailyReport)
            ->with('success', '日報を提出しました。');
    }

    public function approve(\Illuminate\Http\Request $request, \App\Models\DailyReport $dailyReport)
    {
        $this->authorize('approve', $dailyReport);

        // --- サーバ側チェック（提出済み日報の工数が異常なら承認できない） ---
        $totalMinutes = (int) $dailyReport->timeEntries()->sum('minutes');

        if ($totalMinutes <= 0) {
            return redirect()
                ->to(route('reports.show', $dailyReport) . '#approval-panel')
                ->with('error', '工数が0分のため承認できません。工数の未入力が疑われるため、差戻ししてください。');
        }

        if ($totalMinutes > 24 * 60) {
            return redirect()
                ->to(route('reports.show', $dailyReport) . '#approval-panel')
                ->with('error', '合計工数が24時間を超えているため承認できません。内容を確認して差戻ししてください。');
        }

        // --- 承認処理 ---
        $dailyReport->status = 'approved';
        $dailyReport->rejection_reason = null;
        $dailyReport->approved_at = now();
        $dailyReport->approved_by = $request->user()->id;
        $dailyReport->save();

        // 次の承認待ちへ（あなたの実装済みヘルパー）
        return $this->redirectToNextSubmitted($dailyReport, '日報を承認しました。');
    }


    public function reject(Request $request, DailyReport $dailyReport)
    {
        $this->authorize('reject', $dailyReport);

        
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $dailyReport->status = 'rejected';
        $dailyReport->rejection_reason = $data['rejection_reason'];
        $dailyReport->approved_at = now();            // 「確認した日時」として使う（簡易）
        $dailyReport->approved_by = $request->user()->id; // 「差戻しした人」
        $dailyReport->save();

        // return redirect()
        //     ->route('reports.index', ['status' => 'submitted'])
        //     ->with('success', '日報を差戻ししました。');
        return $this->redirectToNextSubmitted($dailyReport, '日報を差戻ししました。');
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

    private function redirectToNextSubmitted(\App\Models\DailyReport $from, string $message)
    {
        // 「今の順番の次」＝より古い（desc順で後ろ）
        $next = \App\Models\DailyReport::query()
            ->where('status', 'submitted')
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
            $next = \App\Models\DailyReport::query()
                ->where('status', 'submitted')
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

}
