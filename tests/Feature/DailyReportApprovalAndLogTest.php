<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\DailyReportStatusLog;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportApprovalAndLogTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmittedReport(User $owner): DailyReport
    {
        $report = new DailyReport();
        $report->user_id = $owner->id;
        $report->report_date = '2026-01-12';
        $report->memo = 'memo';
        $report->status = 'submitted';
        $report->submitted_at = now();
        $report->save();

        return $report;
    }

    private function addTimeEntry(DailyReport $report, int $minutes): void
    {
        $code = 'C' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));
        $name = 'PJ-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));

        $project = \App\Models\Project::create([
            'code' => $code,
            'name' => $name,
            'description' => null,
        ]);

        $entry = new \App\Models\TimeEntry();
        $entry->daily_report_id = $report->id;
        $entry->project_id = $project->id;
        $entry->task = 'task';
        $entry->minutes = $minutes;
        $entry->save();
    }


    public function test_approver_cannot_approve_own_report(): void
    {
        /** @var \App\Models\User $approver */
        $approver = User::factory()->create(['role' => 'approver']);
        $report = $this->makeSubmittedReport($approver);
        $this->addTimeEntry($report, 60);

        $res = $this->actingAs($approver)->post(route('reports.approve', ['dailyReport' => $report]));

        // 実装によって 403 or 302（エラーメッセージで戻す）
        $this->assertTrue(in_array($res->getStatusCode(), [403, 302], true));

        $report->refresh();
        $this->assertSame('submitted', $report->status);

        $this->assertDatabaseMissing('daily_report_status_logs', [
            'daily_report_id' => $report->id,
            'action' => 'approved',
            'actor_id' => $approver->id,
        ]);
    }

    public function test_approver_can_approve_others_submitted_report_and_log_is_created(): void
    {
        /** @var \App\Models\User $approver */
        $approver = User::factory()->create(['role' => 'approver']);
        $member   = User::factory()->create(['role' => 'member']);

        $report = $this->makeSubmittedReport($member);
        $this->addTimeEntry($report, 60);

        $res = $this->actingAs($approver)->post(route('reports.approve', ['dailyReport' => $report]));
        $this->assertTrue(in_array($res->getStatusCode(), [302, 200], true));

        $report->refresh();
        $this->assertSame('approved', $report->status);

        $this->assertDatabaseHas('daily_report_status_logs', [
            'daily_report_id' => $report->id,
            'action' => 'approved',
            'actor_id' => $approver->id,
            'from_status' => 'submitted',
            'to_status' => 'approved',
        ]);
    }

    public function test_approver_cannot_approve_when_total_minutes_is_zero(): void
    {
        /** @var \App\Models\User $approver */
        $approver = User::factory()->create(['role' => 'approver']);
        $member   = User::factory()->create(['role' => 'member']);

        $report = $this->makeSubmittedReport($member);
        // time_entries を入れない＝合計0

        $res = $this->actingAs($approver)->post(route('reports.approve', ['dailyReport' => $report]));
        $this->assertTrue(in_array($res->getStatusCode(), [302, 200], true));

        $report->refresh();
        $this->assertSame('submitted', $report->status);

        $this->assertDatabaseMissing('daily_report_status_logs', [
            'daily_report_id' => $report->id,
            'action' => 'approved',
        ]);
    }

    public function test_reject_creates_log_with_reason(): void
    {
        /** @var \App\Models\User $approver */
        $approver = User::factory()->create(['role' => 'approver']);
        $member   = User::factory()->create(['role' => 'member']);

        $report = $this->makeSubmittedReport($member);
        $this->addTimeEntry($report, 30);

        $res = $this->actingAs($approver)->post(route('reports.reject', ['dailyReport' => $report]), [
            'rejection_reason' => '理由テスト',
        ]);

        $this->assertTrue(in_array($res->getStatusCode(), [302, 200], true));

        $report->refresh();
        $this->assertSame('rejected', $report->status);
        $this->assertSame('理由テスト', $report->rejection_reason);

        $this->assertDatabaseHas('daily_report_status_logs', [
            'daily_report_id' => $report->id,
            'action' => 'rejected',
            'actor_id' => $approver->id,
            'from_status' => 'submitted',
            'to_status' => 'rejected',
            'reason' => '理由テスト',
        ]);
    }
}
