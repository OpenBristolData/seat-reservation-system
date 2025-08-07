<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
// In your reservations table migration
Schema::create('reservations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('seat_id')->constrained()->onDelete('cascade');
    $table->date('reservation_date');
    $table->enum('status', ['active', 'cancelled'])->default('active');
    $table->timestamps();
    
    // Modified unique constraint to only apply to active reservations
    $table->unique(['seat_id', 'reservation_date', 'status']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
