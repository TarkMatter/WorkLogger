<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProjectAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_create_project(): void
    {
        /** @var \App\Models\User $member */
        $member = User::factory()->create(['role' => 'member']);

        $name = 'PJ-' . Str::upper(Str::random(8));
        $code = 'C' . Str::upper(Str::random(6));

        $res = $this->actingAs($member)->post('/projects', [
            'code' => $code,
            'name' => $name,
            'description' => 'desc',
        ]);

        $res->assertForbidden();
        $this->assertDatabaseMissing('projects', ['code' => $code]);
        $this->assertDatabaseMissing('projects', ['name' => $name]);
    }

    public function test_admin_can_create_project(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $name = 'PJ-' . Str::upper(Str::random(8));
        $code = 'C' . Str::upper(Str::random(6));

        $res = $this->actingAs($admin)->post('/projects', [
            'code' => $code,
            'name' => $name,
            'description' => 'desc',
        ]);

        // JSONで201 or 画面系で302 の両対応
        $this->assertTrue(in_array($res->getStatusCode(), [201, 302], true));

        if ($res->getStatusCode() === 302) {
            $res->assertSessionHasNoErrors();
        }

        $this->assertDatabaseHas('projects', [
            'code' => $code,
            'name' => $name,
        ]);
    }
}
