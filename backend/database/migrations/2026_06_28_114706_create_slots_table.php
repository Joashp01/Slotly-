<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A slot is a concrete window of time a provider is available.
     * Customers book against slots. A slot can only be held by one
     * active booking at a time (enforced in the service layer).
     */
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->enum('status', ['available', 'booked'])
                ->default('available')
                ->index();
            $table->timestamps();

            // A provider cannot have two slots starting at the same instant.
            $table->unique(['provider_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
