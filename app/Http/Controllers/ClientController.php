<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Services\ClientService;

class ClientController extends Controller
{
    public function lists()
    {
        $clients = new ClientService()->listClients();

        return response()->json($clients);
    }

    public function store(ClientCreateRequest $request)
    {
        $client = new ClientService()->createClient($request->validated());

        return response()->json($client, 201);
    }

    public function update(Client $client, ClientUpdateRequest $request)
    {
        $client = new ClientService()->updateClient($client, $request->validated());

        return response()->json($client, 200);
    }

    public function destroy(Client $client)
    {
        $client = new ClientService()->deleteClient($client);

        return response()->json(null, 204);
    }
}
