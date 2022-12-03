<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
            if (env('DB_CONNECTION') == 'pgsql') {
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
