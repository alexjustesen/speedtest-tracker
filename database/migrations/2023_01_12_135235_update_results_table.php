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
        Schema::table('results', function (Blueprint $table) {
            $table->float('ping', 8, 3)->nullable()->change();
            $table->unsignedBigInteger('download')->nullable()->change();
            $table->unsignedBigInteger('upload')->nullable()->change();
            $table->json('data')->nullable()->change();

            $table->boolean('successful')->default(true)->after('scheduled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
