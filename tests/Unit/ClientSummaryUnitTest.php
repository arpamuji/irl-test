<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\ClientContract;
use App\Services\ClientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientSummaryUnitTest extends TestCase
{
    use RefreshDatabase;

    private ClientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientService();
    }

    public function test_active_contracts_count_excludes_expired_contracts()
    {
        $client = Client::factory()->create();

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'monthly_value' => 2000,
        ]);

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonths(12),
            'end_date' => now()->subMonth(),
            'monthly_value' => 3000,
        ]);

        $summary = $this->service->getClientSummary($client);

        $this->assertEquals(1, $summary['active_contracts_count']);
    }

    public function test_active_contracts_count_excludes_future_contracts()
    {
        $client = Client::factory()->create();

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'monthly_value' => 2000,
        ]);

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->addMonth(),
            'end_date' => now()->addMonths(3),
            'monthly_value' => 5000,
        ]);

        $summary = $this->service->getClientSummary($client);

        $this->assertEquals(1, $summary['active_contracts_count']);
    }

    public function test_null_dates_are_treated_as_unbounded()
    {
        $client = Client::factory()->create();

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'monthly_value' => 2000,
        ]);

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => null,
            'end_date' => null,
            'monthly_value' => 1000,
        ]);

        $summary = $this->service->getClientSummary($client);

        $this->assertEquals(2, $summary['active_contracts_count']);
    }

    public function test_total_monthly_value_sums_only_active_contracts()
    {
        $client = Client::factory()->create();

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'monthly_value' => 2000,
        ]);

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => null,
            'end_date' => null,
            'monthly_value' => 1000,
        ]);

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->subMonth(),
            'monthly_value' => 3000,
        ]);

        $summary = $this->service->getClientSummary($client);

        $this->assertEquals(3000, $summary['total_monthly_value']);
    }

    public function test_zero_monthly_value_is_valid_and_counted()
    {
        $client = Client::factory()->create();

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'monthly_value' => 0,
        ]);

        ClientContract::factory()->create([
            'client_id' => $client->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'monthly_value' => 2500,
        ]);

        $summary = $this->service->getClientSummary($client);

        $this->assertEquals(2, $summary['active_contracts_count']);
        $this->assertEquals(2500, $summary['total_monthly_value']);
    }
}
