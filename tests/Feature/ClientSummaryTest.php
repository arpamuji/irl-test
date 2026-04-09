<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_summary()
    {
        $user = User::factory()->admin()->create();

        $client = Client::factory()->create();

        /** Active Contract */
        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'monthly_value' => 2000,
        ]);

        /** Unbound Contract */
        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => null,
            'end_date' => null,
            'monthly_value' => 1000,
        ]);

        /** Expired Contract */
        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonths(2),
            'end_date' => now()->subMonth(),
            'monthly_value' => 3000,
        ]);

        /** Future Contract */
        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeek()->addMonths(3),
            'monthly_value' => 5000,
        ]);

        $response = $this->actingAs($user)->get("/api/clients/{$client->id}/summary");

        $response->assertStatus(200)->assertJsonFragment([
            'active_contracts_count' => 2,
            'total_monthly_value' => 3000,
        ]);
    }

    public function test_client_summary_after_deleted()
    {
        $user = User::factory()->admin()->create();
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->get("/api/clients/{$client->id}/summary");
        $response->assertStatus(200)->assertJsonFragment([
            'active_contracts_count' => 0,
            'total_monthly_value' => 0,
        ]);

        $client->delete();

        $response = $this->actingAs($user)->get("/api/clients/{$client->id}/summary");
        $response->assertStatus(404);
    }

    public function test_client_summary_not_found()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get("/api/clients/999/summary");

        $response->assertStatus(404);
    }
}
