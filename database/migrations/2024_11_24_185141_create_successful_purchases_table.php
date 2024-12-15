<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('successful_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to User table
           $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Link to Events table
            $table->decimal('total_price', 10, 2);
            $table->boolean('is_free')->default(false);
            $table->enum('type', ['card', 'gcash', 'paymaya', 'billease', 'grab_pay', 'dob' ,'dob_ubp', 'brankas_bdo', 'brankas_landbank', 'brankas_metrobank'])->default('card')->nullable();
            $table->enum('transaction_type', ['refund', 'transact', 'adjustment', 'chargeback'])->default('transact');
            $table->boolean('is_successful')->default(false);
            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('successful_purchases');
    }
};
