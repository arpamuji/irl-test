<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_list()
    {
        $user = User::factory()->admin()->create();

        Client::factory()->create(['name' => 'Zulu Client']);
        Client::factory()->create(['name' => 'Alpha Client']);
        Client::factory()->create(['name' => 'Romeo Client']);
        Client::factory()->create(['name' => 'Delta Client']);


        $response = $this->actingAs($user)->get('/clients');

        $response->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonPath('0.name', 'Alpha Client')
            ->assertJsonPath('1.name', 'Delta Client')
            ->assertJsonPath('2.name', 'Romeo Client')
            ->assertJsonPath('3.name', 'Zulu Client');
    }

    public function test_client_create()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post('/clients', [
            'src_code' => 'SRC-12345',
            'name' => 'Client Test',
            'short_name' => 'CT',
        ]);
        $response->assertStatus(201)->assertJsonFragment([
            'src_code' => 'SRC-12345',
            'name' => 'Client Test',
            'short_name' => 'CT',
        ]);
    }

    public function test_client_update()
    {
        $client = Client::factory()->create(['name' => 'Old Client Name', 'short_name' => 'OCN']);

        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->put("/clients/{$client->id}", [
            'src_code' => $client->src_code,
            'name' => 'Updated Client Name',
            'short_name' => 'UCN',
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            'src_code' => $client->src_code,
            'name' => 'Updated Client Name',
            'short_name' => 'UCN',
        ]);
    }

    public function test_client_delete()
    {
        $client = Client::factory()->create();

        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->delete("/clients/{$client->id}");
        $response->assertStatus(204);

        $this->assertSoftDeleted($client);
    }
}
