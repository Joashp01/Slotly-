<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
    ) {}

    // Customer: my bookings.
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => BookingResource::collection(
                $this->bookings->listForCustomer($request->user())
            ),
        ]);
    }

    // Provider: bookings made against my services.
    public function received(Request $request): JsonResponse
    {
        return response()->json([
            'data' => BookingResource::collection(
                $this->bookings->listForProvider($request->user())
            ),
        ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $booking = $this->bookings->book(
            $request->user(),
            $data['service_id'],
            $data['slot_id'],
            $data['notes'] ?? null,
        );

        return response()->json(['data' => new BookingResource($booking)], 201);
    }

    public function cancel(Request $request, int $booking): JsonResponse
    {
        $cancelled = $this->bookings->cancel($request->user(), $booking);

        return response()->json(['data' => new BookingResource($cancelled)]);
    }
}
