<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentServiceRepository implements ServiceRepositoryInterface
{
    public function allActive(): Collection
    {
        return Service::query()
            ->where('is_active', true)
            ->with('provider:id,name')
            ->latest()
            ->get();
    }

    public function forProvider(int $providerId): Collection
    {
        return Service::query()
            ->where('provider_id', $providerId)
            ->latest()
            ->get();
    }

    public function find(int $id): ?Service
    {
        return Service::find($id);
    }

    public function create(array $data): Service
    {
        return Service::create($data);
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($data);

        return $service->refresh();
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }
}
