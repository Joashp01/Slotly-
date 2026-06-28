<?php

namespace App\Services;

use App\Enums\SlotStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\Slot;
use App\Models\User;
use App\Repositories\Contracts\SlotRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Business rules around a provider's availability slots.
 */
class SlotService
{
    public function __construct(
        private readonly SlotRepositoryInterface $slots,
    ) {}

    /** @return Collection<int, Slot> */
    public function availableForProvider(int $providerId): Collection
    {
        return $this->slots->availableForProvider($providerId);
    }

    /** @return Collection<int, Slot> */
    public function listForProvider(User $provider): Collection
    {
        return $this->slots->forProvider($provider->id);
    }

    /** @param array{start_at: string, end_at: string} $data */
    public function create(User $provider, array $data): Slot
    {
        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);

        if ($end->lessThanOrEqualTo($start)) {
            throw new BusinessRuleException('A slot must end after it starts.');
        }

        if ($start->isPast()) {
            throw new BusinessRuleException('A slot cannot start in the past.');
        }

        return $this->slots->create([
            'provider_id' => $provider->id,
            'start_at' => $start,
            'end_at' => $end,
            'status' => SlotStatus::Available,
        ]);
    }

    public function delete(User $provider, int $slotId): void
    {
        $slot = $this->ownedSlotOrFail($provider, $slotId);

        if (! $slot->isAvailable()) {
            throw new BusinessRuleException('A booked slot cannot be deleted.');
        }

        $this->slots->delete($slot);
    }

    private function ownedSlotOrFail(User $provider, int $slotId): Slot
    {
        $slot = $this->slots->find($slotId);

        if ($slot === null) {
            throw new BusinessRuleException('Slot not found.', 404);
        }

        if ($slot->provider_id !== $provider->id) {
            throw new BusinessRuleException('You do not own this slot.', 403);
        }

        return $slot;
    }
}
