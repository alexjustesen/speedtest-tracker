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
        Schema::table('settings', function (Blueprint $table): void {
            $table->boolean('locked')->default(false)->change();

            $table->unique(['group', 'name']);

            $table->dropIndex(['group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->boolean('locked')->default(null)->change();

            $table->dropUnique(['group', 'name']);

            $table->index('group');
        });
    }
};
