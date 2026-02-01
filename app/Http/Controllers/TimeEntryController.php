<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\DailyReport;
use App\Models\TimeEntry;
use App\Http\Requests\StoreTimeEntryRequest;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    // フラッシュは共通ヘルパで統一。
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
    public function store(StoreTimeEntryRequest $request, \App\Models\DailyReport $report)
    {
        // 日報の編集権限がある人だけが工数を追加できる
        $this->authorize('update', $report);

        $data = $request->validated();

        $entry = new \App\Models\TimeEntry();
        $entry->dailyReport()->associate($report);
        $entry->project_id = $data['project_id'];
        $entry->minutes = $data['minutes'];
        $entry->task = $data['task'] ?? null;
        $entry->save();

        return $this->redirectRouteWithSuccess(
            'reports.edit',
            $report,
            __('flash.created', ['item' => __('models.time_entry')])
        );
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
    public function destroy(Request $request, DailyReport $report, TimeEntry $entry)
    {
        $this->authorize('update', $report);

        // URLで別の日報のentryを消せないようにする
        abort_if($entry->daily_report_id !== $report->id, 404);

        $entry->delete();

        return redirect()->route('reports.edit', $report);

    }
}
