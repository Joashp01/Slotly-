<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register($request->validated());

        return $this->respondWithToken($result['user'], $result['token'], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth->login($request->validated());

        return $this->respondWithToken($result['user'], $result['token']);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($this->auth->currentUser()),
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return response()->json(['message' => 'Logged out.']);
    }

    public function refresh(): JsonResponse
    {
        $token = $this->auth->refresh();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    private function respondWithToken(object $user, string $token, int $status = 200): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], $status);
    }
}
