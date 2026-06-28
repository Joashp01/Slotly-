<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\User;

class AuthService
{
    /**
     * Register a new account and issue a token straight away.
     *
     * @param  array<string, mixed>  $data
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'password' => $data['password'],
        ]);

        $token = auth('api')->login($user);

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Verify credentials and hand back a token (the "festival wristband").
     *
     * @param  array{email: string, password: string}  $credentials
     * @return array{user: User, token: string}
     */
    public function login(array $credentials): array
    {
        $token = auth('api')->attempt($credentials);

        if ($token === false) {
            throw new BusinessRuleException('These credentials do not match our records.', 401);
        }

        /** @var User $user */
        $user = auth('api')->user();

        return ['user' => $user, 'token' => $token];
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function refresh(): string
    {
        return auth('api')->refresh();
    }

    public function currentUser(): User
    {
        /** @var User $user */
        $user = auth('api')->user();

        return $user;
    }
}
