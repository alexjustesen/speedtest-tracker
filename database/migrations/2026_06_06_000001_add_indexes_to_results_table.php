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
            $table->index('created_at');
            $table->index('status');
            $table->index('download');
            $table->index('upload');
            $table->index('ping');
            $table->index('scheduled');
            $table->index('healthy');
            $table->index('server_id');
            $table->index('server_name');
            $table->index('ip_address');
            $table->index('dispatched_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['download']);
            $table->dropIndex(['upload']);
            $table->dropIndex(['ping']);
            $table->dropIndex(['scheduled']);
            $table->dropIndex(['healthy']);
            $table->dropIndex(['server_id']);
            $table->dropIndex(['server_name']);
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['dispatched_by']);
        });
    }
};
