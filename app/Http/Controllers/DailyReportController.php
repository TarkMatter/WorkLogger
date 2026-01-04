<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Project;
use App\Models\DailyReport;
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

        $query = DailyReport::query()->with('user');

        if (! $user->canApprove()) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->filled('status')) {
                $query->where('status', $request->string('status'));
            }
        }

        $reports = $query
            ->orderByDesc('report_date')
            ->paginate(15);

        return view('reports.index', compact('reports'));
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

        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, DailyReport $report)
    {
        $this->authorize('update', $report);

        $report->load(['timeEntries.project']);

        $projects = Project::query()
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
        $this->authorize('update', $report);

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
        $dailyReport->submitted_at = now();
        $dailyReport->approved_at = null;
        $dailyReport->approved_by = null;
        $dailyReport->save();

        return redirect()
            ->route('reports.show', $dailyReport)
            ->with('success', '日報を提出しました。');
    }

    public function approve(Request $request, DailyReport $dailyReport)
    {
        $this->authorize('approve', $dailyReport);

        $dailyReport->status = 'approved';
        $dailyReport->approved_at = now();
        $dailyReport->approved_by = $request->user()->id;
        $dailyReport->save();

        return redirect()
            ->route('reports.show', $dailyReport)
            ->with('success', '日報を承認しました。');
    }

    public function reject(Request $request, DailyReport $dailyReport)
    {
        $this->authorize('reject', $dailyReport);

        $dailyReport->status = 'rejected';
        $dailyReport->approved_at = now();            // 「確認した日時」として使う（簡易）
        $dailyReport->approved_by = $request->user()->id; // 「差戻しした人」
        $dailyReport->save();

        return redirect()
            ->route('reports.show', $dailyReport)
            ->with('success', '日報を差戻ししました。');
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
}
