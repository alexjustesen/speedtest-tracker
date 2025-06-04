<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DefaultSpeedtestScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the 'schedules' table exists before attempting to insert
        if (! Schema::hasTable('schedules')) {
            return;
        }

        // Only proceed if the SPEEDTEST_SCHEDULE environment variable is set
        if (env('SPEEDTEST_SCHEDULE') === null) {
            return;
        }

        $cron = env('SPEEDTEST_SCHEDULE');
        $servers = env('SPEEDTEST_SERVERS', '');
        $blockedServers = env('SPEEDTEST_BLOCKED_SERVERS', '');
        $skipIps = env('SPEEDTEST_SKIP_IPS', '');
        $interface = env('SPEEDTEST_INTERFACE', '');

        // Base options array
        $options = [
            'cron_expression' => $cron,
            'server_preference' => 'auto',
            'skip_ips' => [],
        ];

        // Add network interface if specified
        if (! empty($interface)) {
            $options['interface'] = $interface;
        }

        // Handle skip_ips (could be JSON string, array, or comma-separated string)
        if (! empty($skipIps)) {
            if (is_string($skipIps)) {
                $decodedSkipIps = json_decode($skipIps, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedSkipIps)) {
                    $options['skip_ips'] = $decodedSkipIps;
                } else {
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
                    $serverIds = array_filter(array_map('trim', explode(',', $servers)));
                    $options['servers'] = array_map(function ($id) {
                        return ['server_id' => $id];
                    }, $serverIds);
                }
            } elseif (is_array($servers)) {
                if (isset($servers[0]) && ! is_array($servers[0])) {
                    $options['servers'] = array_map(function ($id) {
                        return ['server_id' => $id];
                    }, $servers);
                } else {
                    $options['servers'] = $servers;
                }
            }
            $options['server_preference'] = 'prefer';
        }
        // Handle blocked servers (only if 'servers' was not set)
        elseif (! empty($blockedServers)) {
            if (is_string($blockedServers)) {
                $decodedBlocked = json_decode($blockedServers, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedBlocked)) {
                    $options['blocked_servers'] = $decodedBlocked;
                } else {
                    $serverIds = array_filter(array_map('trim', explode(',', $blockedServers)));
                    $options['blocked_servers'] = array_map(function ($id) {
                        return ['server_id' => $id];
                    }, $serverIds);
                }
            } elseif (is_array($blockedServers)) {
                if (isset($blockedServers[0]) && ! is_array($blockedServers[0])) {
                    $options['blocked_servers'] = array_map(function ($id) {
                        return ['server_id' => $id];
                    }, $blockedServers);
                } else {
                    $options['blocked_servers'] = $blockedServers;
                }
            }
            $options['server_preference'] = 'ignore';
        }

        // Leaving this here for when we add them to schedules
        // Build thresholds array from config (fallback to defaults if missing)
        //    $thresholds = [
        //       'enabled' => (bool) config('speedtest.threshold_enabled', false),
        //        'download' => (int) config('speedtest.threshold_download', 0),
        //        'upload' => (int) config('speedtest.threshold_upload', 0),
        //        'ping' => (int) config('speedtest.threshold_ping', 0),
        //    ];

        // Create or update the “Default Speedtest Schedule” record
        Schedule::firstOrCreate(
            [
                'type' => 'Ookla',
                'name' => 'Default Speedtest Schedule',
            ],
            [
                'description' => 'Auto‐created from environment variables.',
                'options' => $options,
                //   'thresholds' => $thresholds,
                'token' => strtolower(Str::random(16)),
                'owned_by_id' => 1,
                'is_active' => true,
            ]
        );
    }
}
