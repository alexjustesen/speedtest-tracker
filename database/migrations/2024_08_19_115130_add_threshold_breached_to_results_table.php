<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            if (! Schema::hasColumn('results', 'threshold_breached')) {
                $table->enum('threshold_breached', ['Passed', 'Failed', 'Unknown'])->default('Unknown');
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            if (Schema::hasColumn('results', 'threshold_breached')) {
                $table->dropColumn('threshold_breached');
            }
        });
    }
};
