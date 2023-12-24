<?php

namespace App\Services;

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
        return config('speedtest.build_version');
    }

    public function getRemoteVersion()
    {
        return cache()->remember($this->cacheKeyRemote, now()->addDay(), function () {
            return shell_exec('curl https://api.github.com/repos/alexjustesen/speedtest-tracker/releases/latest -s | grep \'"tag_name":\' | sed -E \'s/.*"([^"]+)".*/\1/\'');
        });
    }

    public function isOutOfDate()
    {
        $this->getVersions();

        return $this->localVersion != $this->remoteVersion;
    }

    public function flushVersionData()
    {
        try {
            cache()->forget($this->cacheKeyLocal);

            cache()->forget($this->cacheKeyRemote);
        } catch (\Exception $exception) {
            // fail silently, it's ok
        }
    }

    public function getPhpVersion(): string
    {
        return phpversion();
    }
}
