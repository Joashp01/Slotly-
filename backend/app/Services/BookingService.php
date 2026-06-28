<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\Booking;
use App\Models\User;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\SlotRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The heart of Slotly: turning a customer's chosen service + slot into a
 * confirmed booking, while guaranteeing a slot is never booked twice.
 */
class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookings,
        private readonly ServiceRepositoryInterface $services,
        private readonly SlotRepositoryInterface $slots,
    ) {}

    /** @return Collection<int, Booking> */
    public function listForCustomer(User $customer): Collection
    {
        return $this->bookings->forCustomer($customer->id);
    }

    /** @return Collection<int, Booking> */
    public function listForProvider(User $provider): Collection
    {
        return $this->bookings->forProvider($provider->id);
    }

    /**
     * Book a slot for a customer.
     *
     * The whole thing runs in a transaction with a row lock on the slot so
     * that two customers racing for the same slot cannot both succeed.
     */
    public function book(User $customer, int $serviceId, int $slotId, ?string $notes = null): Booking
    {
        $service = $this->services->find($serviceId);

        if ($service === null || ! $service->is_active) {
            throw new BusinessRuleException('That service is not available.', 404);
        }

        return DB::transaction(function () use ($customer, $service, $slotId, $notes) {
            $slot = $this->slots->findForUpdate($slotId);

            if ($slot === null) {
                throw new BusinessRuleException('Slot not found.', 404);
            }

            // The slot and the service must belong to the same provider.
            if ($slot->provider_id !== $service->provider_id) {
                throw new BusinessRuleException('This slot is not offered for that service.');
            }

            // The core rule: an already-booked slot cannot be booked again.
            if (! $slot->isAvailable()) {
                throw new BusinessRuleException('Sorry, this slot has just been taken.', 409);
            }

            $booking = $this->bookings->create([
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'slot_id' => $slot->id,
                'status' => BookingStatus::Confirmed,
                'notes' => $notes,
            ]);

            $this->slots->markBooked($slot);

            return $booking->load(['service', 'slot']);
        });
    }

    /**
     * Cancel a booking the customer owns and free the slot back up.
     */
    public function cancel(User $customer, int $bookingId): Booking
    {
        return DB::transaction(function () use ($customer, $bookingId) {
            $booking = $this->bookings->find($bookingId);

            if ($booking === null) {
                throw new BusinessRuleException('Booking not found.', 404);
            }

            if ($booking->customer_id !== $customer->id) {
                throw new BusinessRuleException('This is not your booking.', 403);
            }

            if ($booking->status === BookingStatus::Cancelled) {
                throw new BusinessRuleException('This booking is already cancelled.');
            }

            $updated = $this->bookings->update($booking, ['status' => BookingStatus::Cancelled]);

            $slot = $this->slots->findForUpdate($booking->slot_id);
            if ($slot !== null) {
                $this->slots->markAvailable($slot);
            }

            return $updated;
        });
    }
}
