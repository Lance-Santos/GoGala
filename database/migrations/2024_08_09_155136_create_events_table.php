<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade'); // Foreign key to organizations table
            $table->string('event_name');
            $table->string('event_slug')->unique();
            $table->timestamp('event_date_start')->nullable();
            $table->timestamp('event_date_end')->nullable();
            $table->time('event_time_start')->nullable();
            $table->time('event_time_end')->nullable();
            $table->decimal('event_latitude', 10, 7); // Latitude
            $table->decimal('event_longitude', 10, 7); // Longitude
            $table->string('event_address_string');
            $table->text('event_description')->nullable();
            $table->string('event_img_url')->nullable();
            $table->string('event_img_banner_url')->nullable();
            $table->enum('event_status', ['Private', 'Public', 'Unlisted']);
            $table->enum('event_type', ['ticket', 'seating','free']);
            $table->boolean('hasEnded')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
