<?php

use App\Models\Speedtest;
use Cron\CronExpression;
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
        Schema::create('speedtests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('schedule')->nullable();
            $table->integer('ookla_server_id')->nullable();
            $table->timestamp('next_run_at');
            $table->timestamps();
        });

        $expression = '*/10 * * * *';
        $cron = new CronExpression($expression);

        Speedtest::create([
            'name' => 'default',
            'schedule' => $expression,
            'next_run_at' => $cron->getNextRunDate()->format('Y-m-d H:i'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('speedtests');
    }
};
