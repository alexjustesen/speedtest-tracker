<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * 🤔 inspired by: https://github.com/ploi/roadmap/blob/main/app/Services/SystemChecker.php
 */
class SystemChecker
{
    public $localVersion;

    public $remoteVersion;

    public string $cacheKeyLocal = 'speedtest-local-version';

    public string $cacheKeyRemote = 'speedtest-remote-version';

    public function getVersions(): self
    {
        $this->localVersion = trim($this->getLocalVersion());

        $this->remoteVersion = trim($this->getRemoteVersion());

        return $this;
    }

    public function getLocalVersion()
    {
        return cache()->remember($this->cacheKeyLocal, now()->addDay(), function () {
            return shell_exec('git describe --tag --abbrev=0');
        });
    }

    public function getRemoteVersion()
    {
        return cache()->remember($this->cacheKeyRemote, now()->addDay(), function () {
            return shell_exec('curl https://api.github.com/repos/alexjustesen/speedtest-tracker/releases/latest -s | grep \'"tag_name":\' | sed -E \'s/.*"([^"]+)".*/\1/\'');
        });
    }

    public function flushVersionData()
    {
        try {
            Cache::forget($this->cacheKeyLocal);

            Cache::forget($this->cacheKeyRemote);
        } catch (\Exception $exception) {
            // fail silently, it's ok
        }
    }

    public function getPhpVersion(): string
    {
        return phpversion();
    }
}
