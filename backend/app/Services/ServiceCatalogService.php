<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Service;
use App\Models\User;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Business rules around the catalog of bookable services.
 */
class ServiceCatalogService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $services,
    ) {}

    /** @return Collection<int, Service> */
    public function listActive(): Collection
    {
        return $this->services->allActive();
    }

    /** @return Collection<int, Service> */
    public function listForProvider(User $provider): Collection
    {
        return $this->services->forProvider($provider->id);
    }

    /** @param array<string, mixed> $data */
    public function create(User $provider, array $data): Service
    {
        $data['provider_id'] = $provider->id;

        return $this->services->create($data);
    }

    /** @param array<string, mixed> $data */
    public function update(User $provider, int $serviceId, array $data): Service
    {
        $service = $this->ownedServiceOrFail($provider, $serviceId);

        return $this->services->update($service, $data);
    }

    public function delete(User $provider, int $serviceId): void
    {
        $service = $this->ownedServiceOrFail($provider, $serviceId);

        $this->services->delete($service);
    }

    /**
     * Fetch a service and confirm the provider owns it.
     */
    private function ownedServiceOrFail(User $provider, int $serviceId): Service
    {
        $service = $this->services->find($serviceId);

        if ($service === null) {
            throw new BusinessRuleException('Service not found.', 404);
        }

        if ($service->provider_id !== $provider->id) {
            throw new BusinessRuleException('You do not own this service.', 403);
        }

        return $service;
    }
}
