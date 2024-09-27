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
            $table->string('threshold_breached_overall')->default('NotChecked');
            $table->string('threshold_breached_download')->default('NotChecked');
            $table->string('threshold_breached_upload')->default('NotChecked');
            $table->string('threshold_breached_ping')->default('NotChecked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn([
                'threshold_breached_overall',
                'threshold_breached_download',
                'threshold_breached_upload',
                'threshold_breached_ping',
            ]);
        });
    }
};
