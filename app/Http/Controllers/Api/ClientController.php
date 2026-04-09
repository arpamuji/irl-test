<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\ClientService;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function summary(Client $client)
    {
        if ($client->trashed()) {
            return response()->json(['message' => 'Client not found.'], 404);
        }

        $summary = new ClientService()->getClientSummary($client);

        return response()->json($summary);
    }
}
