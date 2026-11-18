<?php

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('benchmarks', function (Blueprint $table) {
            $table->id();
            $table->string('metric')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('type')->default(BenchmarkType::Absolute->value);
            $table->float('absolute_value')->nullable();
            $table->float('baseline_value')->nullable();
            $table->float('relative_percentage')->nullable();
            $table->timestamps();
        });

        $this->seedFromExistingThresholdSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benchmarks');
    }

    /**
     * Seed the fixed metric rows, carrying over any existing absolute
     * threshold settings so upgrading users keep identical behavior.
     */
    private function seedFromExistingThresholdSettings(): void
    {
        $settings = DB::table('settings')
            ->where('group', 'threshold')
            ->pluck('payload', 'name')
            ->map(fn ($payload) => json_decode($payload, true));

        $enabled = (bool) ($settings['absolute_enabled'] ?? false);

        $now = now();

        $rows = [
            [
                'metric' => BenchmarkMetric::Download->value,
                'enabled' => $enabled && ($settings['absolute_download'] ?? 0) > 0,
                'absolute_value' => $settings['absolute_download'] ?? null,
            ],
            [
                'metric' => BenchmarkMetric::Upload->value,
                'enabled' => $enabled && ($settings['absolute_upload'] ?? 0) > 0,
                'absolute_value' => $settings['absolute_upload'] ?? null,
            ],
            [
                'metric' => BenchmarkMetric::Ping->value,
                'enabled' => $enabled && ($settings['absolute_ping'] ?? 0) > 0,
                'absolute_value' => $settings['absolute_ping'] ?? null,
            ],
            [
                'metric' => BenchmarkMetric::PacketLoss->value,
                'enabled' => false,
                'absolute_value' => null,
            ],
        ];

        foreach ($rows as $row) {
            DB::table('benchmarks')->insert([
                'metric' => $row['metric'],
                'enabled' => $row['enabled'],
                'type' => BenchmarkType::Absolute->value,
                'absolute_value' => $row['absolute_value'],
                'baseline_value' => null,
                'relative_percentage' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('settings')->where('group', 'threshold')->delete();
    }
};
