<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlotRequest;
use App\Http\Resources\SlotResource;
use App\Services\SlotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function __construct(
        private readonly SlotService $slots,
    ) {}

    // Customer-facing: free upcoming slots for a given provider.
    public function availableForProvider(int $provider): JsonResponse
    {
        return response()->json([
            'data' => SlotResource::collection(
                $this->slots->availableForProvider($provider)
            ),
        ]);
    }

    // Provider: all my slots (booked and free).
    public function mine(Request $request): JsonResponse
    {
        return response()->json([
            'data' => SlotResource::collection(
                $this->slots->listForProvider($request->user())
            ),
        ]);
    }

    public function store(StoreSlotRequest $request): JsonResponse
    {
        $slot = $this->slots->create($request->user(), $request->validated());

        return response()->json(['data' => new SlotResource($slot)], 201);
    }

    public function destroy(Request $request, int $slot): JsonResponse
    {
        $this->slots->delete($request->user(), $slot);

        return response()->json(['message' => 'Slot deleted.']);
    }
}
