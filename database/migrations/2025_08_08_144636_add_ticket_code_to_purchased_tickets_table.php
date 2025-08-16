<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->string('ticket_code')->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('purchased_tickets', function (Blueprint $table) {
            $table->dropColumn('ticket_code');
        });
    }
};
