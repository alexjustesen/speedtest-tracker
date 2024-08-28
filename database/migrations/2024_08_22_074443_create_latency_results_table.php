<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLatencyResultsTable extends Migration
{
    public function up()
    {
        Schema::create('latency_results', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->decimal('min_latency', 8, 2)->nullable();
            $table->decimal('avg_latency', 8, 2)->nullable();
            $table->decimal('max_latency', 8, 2)->nullable();
            $table->integer('packet_loss')->nullable();
            $table->integer('ping_count');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('latency_results');
    }
}
