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
        Schema::create('purchased_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to User table
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Link to Events table
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('cascade');
            $table->foreignId('successful_purchases_id')->nullable()->constrained('successful_purchases')->onDelete('cascade');
            $table->string('qr_code');
            $table->string('payment_id')->nullable()->default(null);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('seat_id')->default(null)->nullable();
            $table->string('seat_identifier')->default(null)->nullable();
            $table->boolean('is_blacklisted')->default(false)->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchased_tickets');
    }
};
