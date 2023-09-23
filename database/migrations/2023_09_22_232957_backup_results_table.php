<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
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
            Schema::rename('results', 'results_bak_0-12-0');
        }

        if (! Schema::hasTable('results')) {
            /**
             * Create a new results table based on the current DDL.
             */
            Schema::create('results', function (Blueprint $table) {
                $table->id();
                $table->float('ping', 8, 3)->nullable();
                $table->unsignedBigInteger('download')->nullable(); // will be stored in bytes
                $table->unsignedBigInteger('upload')->nullable(); // will be stored in bytes
                $table->integer('server_id')->nullable();
                $table->string('server_host')->nullable();
                $table->string('server_name')->nullable();
                $table->string('url')->nullable();
                $table->text('comments')->nullable();
                $table->boolean('scheduled')->default(false);
                $table->boolean('successful')->default(true);
                $table->json('data')->nullable(); // is a dump of the cli results in case we want more fields later
                $table->timestamps();
            });
        }

        if (Schema::hasTable('results')) {
            /**
             * Copy backup data to the new results table and reformat it.
             */
            DB::table('results_bak_0-12-0')->chunkById(100, function ($results) {
                foreach ($results as $result) {
                    $result->data = json_decode($result->data);
                    $result->updated_at = now();

                    $result = collect($result);

                    DB::table('results')->insert($result->toArray());
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
