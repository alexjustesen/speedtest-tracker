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
        if (Schema::hasTable('results')) {
            /**
             * Rename the existing table so that a backup copy exists.
             */
            Schema::rename('results', 'results_bak_bad_json');
        }

        if (! Schema::hasTable('results')) {
            /**
             * Create a new results table based on a new DDL schema.
             */
            Schema::create('results', function (Blueprint $table) {
                $table->id();
                $table->string('service')->default('ookla');
                $table->float('ping', 8, 3)->nullable();
                $table->unsignedBigInteger('download')->nullable();
                $table->unsignedBigInteger('upload')->nullable();
                $table->text('comments')->nullable();
                $table->json('data')->nullable();
                $table->string('status')->default('pending');
                $table->boolean('scheduled')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('results');

        if (! Schema::hasTable('results')) {
            Schema::rename('results_bak_bad_json', 'results');
        }
    }
};
