<?php

use App\Models\Schedule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owned_by_id')->nullable();
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->json('options')->nullable();
            $table->json('thresholds')->nullable();
            $table->string('token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('failed_runs')->default(0);
            $table->dateTime('next_run_at')->nullable();
            $table->timestamps();
            $table->foreign('owned_by_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        // Only create speedtest schedule if SPEEDTEST_SCHEDULE is set
        if (env('SPEEDTEST_SCHEDULE') !== null) {
            $cron = env('SPEEDTEST_SCHEDULE');
            $servers = env('SPEEDTEST_SERVERS', '');
            $blockedServers = env('SPEEDTEST_BLOCKED_SERVERS', '');
            $skipIps = env('SPEEDTEST_SKIP_IPS', '');
            $interface = env('SPEEDTEST_INTERFACE', '');

            $options = [
                'cron_expression' => $cron,
                'server_preference' => 'auto',
                'skip_ips' => [],
            ];

            // Add network interface if specified
            if (! empty($interface)) {
                $options['interface'] = $interface;
            }

            // Handle skip_ips - could be JSON string, array, or comma-separated string
            if (! empty($skipIps)) {
                if (is_string($skipIps)) {
                    // Try to decode as JSON first
                    $decodedSkipIps = json_decode($skipIps, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedSkipIps)) {
                        $options['skip_ips'] = $decodedSkipIps;
                    } else {
                        // Fall back to comma-separated string handling
                        $options['skip_ips'] = array_filter(array_map('trim', explode(',', $skipIps)));
                    }
                } elseif (is_array($skipIps)) {
                    $options['skip_ips'] = $skipIps;
                }
            }

            // Handle servers
            if (! empty($servers)) {
                if (is_string($servers)) {
                    $decodedServers = json_decode($servers, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedServers)) {
                        $options['servers'] = $decodedServers;
                    } else {
                        // For comma-separated strings, convert to array of server objects
                        $serverIds = array_filter(array_map('trim', explode(',', $servers)));
                        $options['servers'] = array_map(function ($serverId) {
                            return ['server_id' => $serverId];
                        }, $serverIds);
                    }
                } elseif (is_array($servers)) {
                    // If it's already an array, make sure each item has server_id format
                    if (isset($servers[0]) && ! is_array($servers[0])) {
                        $options['servers'] = array_map(function ($serverId) {
                            return ['server_id' => $serverId];
                        }, $servers);
                    } else {
                        $options['servers'] = $servers;
                    }
                }
                $options['server_preference'] = 'prefer';
            }
            // Handle blocked servers
            elseif (! empty($blockedServers)) {
                if (is_string($blockedServers)) {
                    $decodedBlockedServers = json_decode($blockedServers, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedBlockedServers)) {
                        $options['blocked_servers'] = $decodedBlockedServers;
                    } else {
                        // For comma-separated strings, convert to array of server objects
                        $serverIds = array_filter(array_map('trim', explode(',', $blockedServers)));
                        $options['blocked_servers'] = array_map(function ($serverId) {
                            return ['server_id' => $serverId];
                        }, $serverIds);
                    }
                } elseif (is_array($blockedServers)) {
                    // If it's already an array, make sure each item has server_id format
                    if (isset($blockedServers[0]) && ! is_array($blockedServers[0])) {
                        $options['blocked_servers'] = array_map(function ($serverId) {
                            return ['server_id' => $serverId];
                        }, $blockedServers);
                    } else {
                        $options['blocked_servers'] = $blockedServers;
                    }
                }
                $options['server_preference'] = 'ignore';
            }

            $thresholds = [
                'enabled' => (bool) config('speedtest.threshold_enabled'),
                'download' => (int) config('speedtest.threshold_download'),
                'upload' => (int) config('speedtest.threshold_upload'),
                'ping' => (int) config('speedtest.threshold_ping'),
            ];

            Schedule::create([
                'type' => 'Ookla',
                'name' => 'Default Speedtest Schedule',
                'description' => 'Auto-created from environment variables.',
                'options' => $options,
                'thresholds' => $thresholds,
                'failed_runs' => 0,
                'token' => strtolower(Str::random(16)),
                'owned_by_id' => '1',
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
