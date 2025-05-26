<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataIntegrationSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('data_integration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->boolean('enabled')->default(false);
            $table->string('url')->nullable();
            $table->string('org')->nullable();
            $table->string('bucket')->nullable();
            $table->string('token')->nullable();
            $table->boolean('verify_ssl')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_integration_settings');
    }
}
