<?php

namespace App\Providers;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\SlotRepositoryInterface;
use App\Repositories\Eloquent\EloquentBookingRepository;
use App\Repositories\Eloquent\EloquentServiceRepository;
use App\Repositories\Eloquent\EloquentSlotRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bind each repository contract to its Eloquent implementation.
     *
     * This is the Dependency Inversion seam: the rest of the app depends
     * on the interfaces, and swapping the storage engine means changing
     * only these bindings.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        ServiceRepositoryInterface::class => EloquentServiceRepository::class,
        SlotRepositoryInterface::class => EloquentSlotRepository::class,
        BookingRepositoryInterface::class => EloquentBookingRepository::class,
    ];
}
