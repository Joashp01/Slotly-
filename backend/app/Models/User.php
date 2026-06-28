<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

#[Fillable(['name', 'email', 'phone', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * The identifier stored in the JWT "sub" claim.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Custom claims embedded in every issued token.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role->value,
        ];
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    // Services this user offers (only meaningful for providers).
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    // Availability slots this user owns (only meaningful for providers).
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class, 'provider_id');
    }

    // Bookings this user made (only meaningful for customers).
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }
}
