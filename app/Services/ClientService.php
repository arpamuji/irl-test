<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function listClients()
    {
        $clients = Client::with('contracts')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return $clients;
    }

    public function getClientSummary(Client $client)
    {
        $client->load('contracts');

        $activeContracts = $client->contracts->filter(function ($contract) {
            $now = now()->startOfDay();

            $hasStarted = is_null($contract->start_date) || $contract->start_date->startOfDay() <= $now;
            $hasNotEnded = is_null($contract->end_date) || $contract->end_date->startOfDay() >= $now;

            return $hasStarted && $hasNotEnded;
        });

        return [
            'client' => $client,
            'contracts' => $client->contracts,
            'total_monthly_value' => $activeContracts->sum('monthly_value'),
            'active_contracts_count' => $activeContracts->count(),
        ];
    }

    public function createClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            $client = Client::create($data);

            if (isset($data['contracts']) && is_array($data['contracts'])) {
                $client->contracts()->createMany($data['contracts']);
            }

            return $client->load('contracts');
        });
    }

    public function updateClient(Client $client, array $data)
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update($data);

            if (isset($data['contracts']) && is_array($data['contracts'])) {
                $contractIds = collect($data['contracts'])
                    ->pluck('id')
                    ->filter()
                    ->toArray();

                $client->contracts()->whereNotIn('id', $contractIds)->delete();

                foreach ($data['contracts'] as $contract) {
                    $client->contracts()
                        ->updateOrCreate(['id' => $contract['id'] ?? null], $contract);
                }
            }

            return $client->fresh('contracts');
        });
    }

    public function deleteClient(Client $client)
    {
        return DB::transaction(function () use ($client) {
            $client->contracts()->delete();
            $client->delete();

            return $client;
        });
    }
}
