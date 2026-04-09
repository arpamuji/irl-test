<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_user_gets_403()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/clients');

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/clients');

        $response->assertStatus(200);
    }
}
