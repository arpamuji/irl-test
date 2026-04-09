<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_cascade_when_client_deleted()
    {
        $client = Client::factory()->hasContracts(5)->create();
        $contract = $client->contracts()->first();

        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->delete("/clients/{$client->id}");
        $response->assertStatus(204);

        $this->assertSoftDeleted($client);
        $this->assertSoftDeleted($contract);
    }

    public function test_client_create_with_contracts()
    {
        $user = User::factory()->admin()->create();

        Client::factory()
            ->hasContracts(2)
            ->create([
                'name' => 'Client Alpha',
            ]);

        Client::factory()
            ->create([
                'name' => 'Client Beta',
            ]);

        $response = $this->actingAs($user)->get('/clients');
        $response->assertStatus(200)
            ->assertJsonPath('0.name', 'Client Alpha')
            ->assertJsonCount(2, '0.contracts')
            ->assertJsonPath('1.name', 'Client Beta');
    }

    public function test_src_code_is_unique_reusable_if_deleted()
    {
        $user = User::factory()->admin()->create();
        $clientData1 = [
            'src_code' => 'SRC-54321',
            'name' => 'Client Alpha',
            'short_name' => 'CA',
        ];
        $clientData2 = [
            'src_code' => 'SRC-54321',
            'name' => 'Client Beta',
            'short_name' => 'CB',
        ];

        $client1 = Client::factory()->create($clientData1);

        $response = $this->actingAs($user)->postJson('/clients', $clientData2);

        $response->assertStatus(422)->assertJsonValidationErrors('src_code');

        $response = $this->actingAs($user)->delete("/clients/{$client1->id}");
        $response->assertStatus(204);

        $this->assertSoftDeleted($client1->fresh());

        $response = $this->actingAs($user)->postJson('/clients', $clientData2);
        $response->assertStatus(201)->assertJsonFragment($clientData2);
    }
}
