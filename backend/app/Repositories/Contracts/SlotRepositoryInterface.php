<?php

namespace App\Repositories\Contracts;

use App\Models\Slot;
use Illuminate\Support\Collection;

interface SlotRepositoryInterface
{
    /** @return Collection<int, Slot> */
    public function availableForProvider(int $providerId): Collection;

    /** @return Collection<int, Slot> */
    public function forProvider(int $providerId): Collection;

    public function find(int $id): ?Slot;

    /**
     * Fetch a slot with a row-level lock for the current transaction.
     * Used to prevent two customers booking the same slot at once.
     */
    public function findForUpdate(int $id): ?Slot;

    /** @param array<string, mixed> $data */
    public function create(array $data): Slot;

    public function markBooked(Slot $slot): void;

    public function markAvailable(Slot $slot): void;

    public function delete(Slot $slot): void;
}
