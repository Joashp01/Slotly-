<?php

namespace App\Models;

use App\Enums\SlotStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['provider_id', 'start_at', 'end_at', 'status'])]
class Slot extends Model
{
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'status' => SlotStatus::class,
        ];
    }

    public function isAvailable(): bool
    {
        return $this->status === SlotStatus::Available;
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    // The active booking holding this slot, if any.
    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
