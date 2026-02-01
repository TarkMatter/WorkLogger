<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportFlashTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_denied_sets_flash_error(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'member']);

        $report = new DailyReport();
        $report->user_id = $user->id;
        $report->report_date = '2026-02-01';
        $report->status = 'submitted';
        $report->submitted_at = now();
        $report->save();

        $res = $this->actingAs($user)->get(route('reports.edit', $report));

        $res->assertRedirect(route('reports.show', $report));
        $res->assertSessionHas('flash.type', 'error');
        $res->assertSessionHas('flash.message');
    }

    public function test_approve_own_report_sets_flash_error(): void
    {
        /** @var \App\Models\User $approver */
        $approver = User::factory()->create(['role' => 'approver']);

        $report = new DailyReport();
        $report->user_id = $approver->id;
        $report->report_date = '2026-02-02';
        $report->status = 'submitted';
        $report->submitted_at = now();
        $report->save();

        $res = $this->actingAs($approver)->post(route('reports.approve', ['report' => $report]));

        $res->assertRedirect(route('reports.show', $report));
        $res->assertSessionHas('flash.type', 'error');
        $res->assertSessionHas('flash.message');
    }
}
