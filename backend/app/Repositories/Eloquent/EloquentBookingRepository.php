<?php

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function forCustomer(int $customerId): Collection
    {
        return Booking::query()
            ->where('customer_id', $customerId)
            ->with(['service', 'slot'])
            ->latest()
            ->get();
    }

    public function forProvider(int $providerId): Collection
    {
        return Booking::query()
            ->whereHas('service', fn ($q) => $q->where('provider_id', $providerId))
            ->with(['service', 'slot', 'customer:id,name,phone'])
            ->latest()
            ->get();
    }

    public function find(int $id): ?Booking
    {
        return Booking::find($id);
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function update(Booking $booking, array $data): Booking
    {
        $booking->update($data);

        return $booking->refresh();
    }
}
