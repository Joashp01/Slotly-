<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A booking links a customer to a service and the slot they reserved.
     * The slot's own status guards against double-booking.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();
            $table->foreignId('slot_id')
                ->constrained('slots')
                ->cascadeOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                ->default('pending')
                ->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
