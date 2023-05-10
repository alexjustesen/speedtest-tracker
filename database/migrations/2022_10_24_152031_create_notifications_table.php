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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');

            /**
             * PostgreSQL doesn't support "text" column type so we need to use the 'json' type instead.
             *
             * Docs: https://filamentphp.com/docs/2.x/notifications/database-notifications
             */
            if (config('database.default') == 'pgsql') {
                $table->json('data');
            } else {
                $table->text('data');
            }

            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
