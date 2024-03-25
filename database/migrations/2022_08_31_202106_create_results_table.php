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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->float('ping', 8, 3);
            $table->unsignedBigInteger('download'); // will be stored in bytes
            $table->unsignedBigInteger('upload'); // will be stored in bytes
            $table->integer('server_id')->nullable();
            $table->string('server_host')->nullable();
            $table->string('server_name')->nullable();
            $table->string('url')->nullable();
            $table->boolean('scheduled')->default(false);
            $table->json('data'); // is a dump of the cli output in case we want more fields later
            $table->timestamp('created_at')
                ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
