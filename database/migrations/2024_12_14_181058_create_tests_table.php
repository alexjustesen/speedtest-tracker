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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owned_by_id')->nullable();
            $table->string('type')->default('speedtest');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->json('options')->nullable();
            $table->string('token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('next_run_at')->nullable();
            $table->timestamps();

            $table->foreign('owned_by_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};