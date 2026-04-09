<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_src_code_uniqueness()
    {
        $user = User::factory()->admin()->create();

        Client::factory()->create([
            'src_code' => 'SRC-54321',
            'name' => 'Client Alpha',
            'short_name' => 'CA',
        ]);

        $response = $this->actingAs($user)->postJson('/clients', [
            'src_code' => 'SRC-54321',
            'name' => 'Client Beta',
            'short_name' => 'CB',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['src_code']);
    }

    public function test_end_date_cannot_be_before_start_date()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->postJson('/clients', [
            'src_code' => 'SRC-54321',
            'name' => 'Client Alpha',
            'contracts' => [
                [
                    'start_date' => '2026-01-31',
                    'end_date' => '2025-12-01',
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contracts.0.start_date']);
    }

    public function test_required_fields()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->postJson('/clients', [
            'src_code' => 'SRC-54321',
            'name' => null
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $response = $this->actingAs($user)->postJson('/clients', [
            'src_code' => 'SRC-54321',
            'name' => 'Client Alpha',
            'contracts' => [
                [
                    'name' => null,
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contracts.0.name']);
    }

    public function test_negative_monthly_value()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->postJson('/clients', [
            'src_code' => 'SRC-54321',
            'name' => 'Client Alpha',
            'contracts' => [
                [
                    'name' => 'Contract Negative',
                    'monthly_value' => -1000,
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contracts.0.monthly_value']);
    }
}
