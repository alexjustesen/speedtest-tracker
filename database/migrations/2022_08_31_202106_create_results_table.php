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
            $table->string('service')->default('ookla');
            $table->float('ping', 8, 3)->nullable();
            $table->unsignedBigInteger('download')->nullable();
            $table->unsignedBigInteger('upload')->nullable();
            $table->text('comments')->nullable();
            $table->json('data')->nullable();
            $table->string('status');
            $table->boolean('scheduled')->default(false);
            $table->timestamps();
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
