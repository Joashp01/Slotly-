<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    /** @return Collection<int, Booking> */
    public function forCustomer(int $customerId): Collection;

    /** @return Collection<int, Booking> */
    public function forProvider(int $providerId): Collection;

    public function find(int $id): ?Booking;

    /** @param array<string, mixed> $data */
    public function create(array $data): Booking;

    /** @param array<string, mixed> $data */
    public function update(Booking $booking, array $data): Booking;
}
