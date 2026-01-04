<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\DailyReport;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TimeEntryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DailyReport $dailyReport)
    {
        // 日報を編集できる人だけ＝本人かつ draft/rejected（Policy）
        $this->authorize('update', $dailyReport);

        $data = $request->validate([
            'project_id' => [
                'required',
                'integer',
                // 自分の案件しか選べない
                Rule::exists('projects', 'id')->where(fn ($q) => $q->where('user_id', $request->user()->id)),
            ],
            'task' => ['nullable', 'string', 'max:255'],
            'minutes' => ['required', 'integer', 'min:1', 'max:' . (24 * 60)],
        ]);

        $entry = new TimeEntry();
        $entry->project_id = $data['project_id'];
        $entry->task = $data['task'] ?? null;
        $entry->minutes = $data['minutes'];
        $entry->dailyReport()->associate($dailyReport);
        $entry->save();

        return redirect()->route('reports.edit', $dailyReport);
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeEntry $timeEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeEntry $timeEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeEntry $timeEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, DailyReport $dailyReport, TimeEntry $entry)
    {
        $this->authorize('update', $dailyReport);

        // URLで別の日報のentryを消せないようにする
        abort_if($entry->daily_report_id !== $dailyReport->id, 404);

        $entry->delete();

        return redirect()->route('reports.edit', $dailyReport);

    }
}
