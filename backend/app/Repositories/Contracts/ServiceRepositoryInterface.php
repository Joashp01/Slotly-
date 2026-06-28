<?php

namespace App\Repositories\Contracts;

use App\Models\Service;
use Illuminate\Support\Collection;

interface ServiceRepositoryInterface
{
    /** @return Collection<int, Service> */
    public function allActive(): Collection;

    /** @return Collection<int, Service> */
    public function forProvider(int $providerId): Collection;

    public function find(int $id): ?Service;

    /** @param array<string, mixed> $data */
    public function create(array $data): Service;

    /** @param array<string, mixed> $data */
    public function update(Service $service, array $data): Service;

    public function delete(Service $service): void;
}
