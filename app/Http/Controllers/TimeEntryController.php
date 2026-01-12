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
    public function store(\Illuminate\Http\Request $request, \App\Models\DailyReport $dailyReport)
    {
        // 日報の編集権限がある人だけが工数を追加できる
        $this->authorize('update', $dailyReport);

        $data = $request->validate([
            'project_id' => [
                'required',
                'integer',
                // projects は全体共有：user_idで絞らない
                \Illuminate\Validation\Rule::exists('projects', 'id'),
            ],
            'minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'task' => ['nullable', 'string', 'max:255'],
        ]);

        $entry = new \App\Models\TimeEntry();
        $entry->dailyReport()->associate($dailyReport);
        $entry->project_id = $data['project_id'];
        $entry->minutes = $data['minutes'];
        $entry->task = $data['task'] ?? null;
        $entry->save();

        return redirect()
            ->route('reports.edit', $dailyReport)
            ->with('success', '工数を追加しました。');
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
