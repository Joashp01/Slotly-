<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceCatalogService $catalog,
    ) {}

    // Public: any authenticated user can browse the active catalog.
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => ServiceResource::collection($this->catalog->listActive()),
        ]);
    }

    // Provider: the services I offer.
    public function mine(Request $request): JsonResponse
    {
        return response()->json([
            'data' => ServiceResource::collection(
                $this->catalog->listForProvider($request->user())
            ),
        ]);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->catalog->create($request->user(), $request->validated());

        return response()->json(['data' => new ServiceResource($service)], 201);
    }

    public function update(UpdateServiceRequest $request, int $service): JsonResponse
    {
        $updated = $this->catalog->update($request->user(), $service, $request->validated());

        return response()->json(['data' => new ServiceResource($updated)]);
    }

    public function destroy(Request $request, int $service): JsonResponse
    {
        $this->catalog->delete($request->user(), $service);

        return response()->json(['message' => 'Service deleted.']);
    }
}
