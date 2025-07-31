<?php

namespace App\Services\GitHub;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class Repository
{
    /**
     * Get the latest tagged version from the GitHub repository.
     *
     * @return string|false Returns the version tag (e.g., "v1.2.3") or false on failure
     */
    public static function getLatestVersion(): string|false
    {
        return Cache::remember('github.latest_version', now()->addHour(), function () {
            try {
                $response = Http::retry(3, 100)
                    ->timeout(10)
                    ->withHeaders([
                        'Accept' => 'application/vnd.github.v3+json',
                        'User-Agent' => 'speedtest-tracker',
                    ])
                    ->get('https://api.github.com/repos/alexjustesen/speedtest-tracker/releases/latest');

                if (!$response->successful()) {
                    Log::warning('Failed to fetch latest version from GitHub API', [
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);

                    return false;
                }

                $data = $response->json();

                if (!isset($data['tag_name'])) {
                    Log::warning('GitHub API response missing tag_name field', ['response' => $data]);

                    return false;
                }

                return $data['tag_name'];
            } catch (Throwable $e) {
                Log::error('Exception occurred while fetching latest version from GitHub', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);

                return false;
            }
        });
    }

    /**
     * Check if a newer version is available.
     *
     * @return bool Returns true if a newer version is available, false if up to date or unable to determine
     */
    public static function updateAvailable(): bool
    {
        $currentVersion = config('speedtest.build_version');
        $latestVersion = self::getLatestVersion();

        if ($latestVersion === false) {
            return false; // Unable to determine, assume no update available
        }

        // Normalize versions by removing 'v' prefix for comparison
        $normalizedCurrent = ltrim($currentVersion, 'v');
        $normalizedLatest = ltrim($latestVersion, 'v');

        // Use version_compare to properly compare semantic versions
        $comparison = version_compare($normalizedLatest, $normalizedCurrent);

        return $comparison > 0; // 1 = latest is newer, 0 = equal, -1 = latest is older
    }


}
