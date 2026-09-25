<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferentielsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_referentiels_page(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Test',
            'email' => 'admin.test@ofppt.ma',
        ]);

        $this->actingAs($user)
            ->get('/referentiels')
            ->assertOk()
            ->assertSee('Paramétrage');
    }
}
