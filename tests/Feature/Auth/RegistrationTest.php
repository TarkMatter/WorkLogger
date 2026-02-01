<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_requires_admin(): void
    {
        $guestResponse = $this->get('/register');
        $guestResponse->assertRedirect(route('login'));

        /** @var \App\Models\User $member */
        $member = User::factory()->create(['role' => 'member']);
        $memberResponse = $this->actingAs($member)->get('/register');
        $memberResponse->assertStatus(403);

        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get('/register');
        $adminResponse->assertStatus(200);
    }

    public function test_admin_can_register_new_users_without_switching_session(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertAuthenticatedAs($admin);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'member',
        ]);
    }
}
