<?php

namespace App\Repositories\Eloquent;

use App\Enums\SlotStatus;
use App\Models\Slot;
use App\Repositories\Contracts\SlotRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentSlotRepository implements SlotRepositoryInterface
{
    public function availableForProvider(int $providerId): Collection
    {
        return Slot::query()
            ->where('provider_id', $providerId)
            ->where('status', SlotStatus::Available)
            ->where('start_at', '>', now())
            ->orderBy('start_at')
            ->get();
    }

    public function forProvider(int $providerId): Collection
    {
        return Slot::query()
            ->where('provider_id', $providerId)
            ->orderBy('start_at')
            ->get();
    }

    public function find(int $id): ?Slot
    {
        return Slot::find($id);
    }

    public function findForUpdate(int $id): ?Slot
    {
        return Slot::query()->whereKey($id)->lockForUpdate()->first();
    }

    public function create(array $data): Slot
    {
        return Slot::create($data);
    }

    public function markBooked(Slot $slot): void
    {
        $slot->update(['status' => SlotStatus::Booked]);
    }

    public function markAvailable(Slot $slot): void
    {
        $slot->update(['status' => SlotStatus::Available]);
    }

    public function delete(Slot $slot): void
    {
        $slot->delete();
    }
}
