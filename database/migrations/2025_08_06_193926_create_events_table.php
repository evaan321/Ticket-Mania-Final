<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->text('description')->nullable(false);
            $table->timestamp('event_date')->nullable(false);
            $table->string('location')->nullable(false);
            $table->float('latitude', 10, 6)->nullable();  // Latitude for location
            $table->float('longitude', 10, 6)->nullable();
            $table->string('category')->nullable(false);
            $table->string('image')->nullable(false);
            // Longitude for location
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Organizer reference
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
