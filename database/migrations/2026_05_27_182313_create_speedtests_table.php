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
        Schema::create('speedtests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('servers')->nullable();
            $table->json('blocked_servers')->nullable();
            $table->json('server_labels')->nullable();
            $table->string('interface')->nullable();
            $table->json('skip_ips')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speedtests');
    }
};
