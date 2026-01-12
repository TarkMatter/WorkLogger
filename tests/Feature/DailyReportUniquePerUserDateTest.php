<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportUniquePerUserDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_date_can_be_created_by_different_users(): void
    {
        /** @var \App\Models\User $u1 */
        $u1 = User::factory()->create(['role' => 'member']);
        /** @var \App\Models\User $u2 */
        $u2 = User::factory()->create(['role' => 'member']);

        $date = '2026-01-12';

        $r1 = $this->actingAs($u1)->post(route('reports.store'), [
            'report_date' => $date,
            'memo' => 'u1 memo',
        ]);
        $this->assertTrue(in_array($r1->getStatusCode(), [302, 201], true));

        $r2 = $this->actingAs($u2)->post(route('reports.store'), [
            'report_date' => $date,
            'memo' => 'u2 memo',
        ]);
        $this->assertTrue(in_array($r2->getStatusCode(), [302, 201], true));

        $this->assertDatabaseHas('daily_reports', ['user_id' => $u1->id, 'report_date' => $date]);
        $this->assertDatabaseHas('daily_reports', ['user_id' => $u2->id, 'report_date' => $date]);
    }

    public function test_same_user_cannot_create_two_reports_on_same_date(): void
    {
        /** @var \App\Models\User $u */
        $u = User::factory()->create(['role' => 'member']);
        $date = '2026-01-12';

        $this->actingAs($u)->post(route('reports.store'), [
            'report_date' => $date,
            'memo' => 'first',
        ]);

        $res = $this->actingAs($u)
            ->from(route('reports.create'))
            ->post(route('reports.store'), [
                'report_date' => $date,
                'memo' => 'second',
            ]);

        $res->assertRedirect(route('reports.create'));
        $res->assertSessionHasErrors(['report_date']);

        $count = DailyReport::query()->where('user_id', $u->id)->where('report_date', $date)->count();
        $this->assertSame(1, $count);
    }
}
