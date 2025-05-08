<?php

namespace App\Models\Traits;

use App\Helpers\Bitrate;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;

trait ResultDataAttributes
{
    /**
     * Get the result's download in bits.
     */
    protected function downloadBits(): Attribute
    {
        return Attribute::make(
            get: fn (): null|int|float => ! blank($this->download) ? Bitrate::bytesToBits($this->download) : null,
        );
    }

    /**
     * Get the result's download jitter in milliseconds.
     */
    protected function downloadlatencyJitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.jitter'),
        );
    }

    /**
     * Get the result's download latency high in milliseconds.
     */
    protected function downloadlatencyHigh(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.high'),
        );
    }

    /**
     * Get the result's download latency low in milliseconds.
     */
    protected function downloadlatencyLow(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.low'),
        );
    }

    /**
     * Get the result's download latency iqm in milliseconds.
     */
    protected function downloadlatencyiqm(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.latency.iqm'),
        );
    }

    /**
     * Get the result's download jitter in milliseconds.
     */
    protected function errorMessage(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'message', ''),
        );
    }

    /**
     * Get the result's external ip address (yours).
     */
    protected function ipAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'interface.externalIp'),
        );
    }

    /**
     * Get the result's isp tied to the external ip address.
     */
    protected function isp(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'isp'),
        );
    }

    /**
     * Get the result's packet loss as a percentage.
     */
    protected function packetLoss(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'packetLoss'),
        );
    }

    /**
     * Get the result's ping jitter in milliseconds.
     */
    protected function pingJitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'ping.jitter'),
        );
    }

    /**
     * Get the result's server ID.
     */
    protected function resultUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'result.url'),
        );
    }

    /**
     * Get the result's server host.
     */
    protected function serverHost(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.host'),
        );
    }

    /**
     * Get the result's server ID.
     */
    protected function serverId(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.id'),
        );
    }

    /**
     * Get the result's server name.
     */
    protected function serverName(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.name'),
        );
    }

    /**
     * Get the result's server location.
     */
    protected function serverLocation(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.location'),
        );
    }

    /**
     * Get the result's upload in bits.
     */
    protected function uploadBits(): Attribute
    {
        return Attribute::make(
            get: fn (): null|int|float => ! blank($this->upload) ? Bitrate::bytesToBits($this->upload) : null,
        );
    }

    /**
     * Get the result's upload jitter in milliseconds.
     */
    protected function uploadlatencyjitter(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.jitter'),
        );
    }

    /**
     * Get the result's upload latency high in milliseconds.
     */
    protected function uploadlatencyHigh(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.high'),
        );
    }

    /**
     * Get the result's upload latency low in milliseconds.
     */
    protected function uploadlatencyLow(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.low'),
        );
    }

    /**
     * Get the result's upload latency iqm in milliseconds.
     */
    protected function uploadlatencyiqm(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.latency.iqm'),
        );
    }

    /**
     * Get the result's ping low latency in milliseconds.
     */
    protected function pingLow(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'ping.low'),
        );
    }

    /**
     * Get the result's ping high latency in milliseconds.
     */
    protected function pingHigh(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'ping.high'),
        );
    }

    /**
     * Get the result's download elapsed time in milliseconds.
     */
    protected function downloadElapsed(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'download.elapsed'),
        );
    }

    /**
     * Get the result's upload elapsed time in milliseconds.
     */
    protected function uploadElapsed(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'upload.elapsed'),
        );
    }

    /**
     * Get the result's server port.
     */
    protected function serverPort(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.port'),
        );
    }

    /**
     * Get the result's server IP address.
     */
    protected function serverIp(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.ip'),
        );
    }

    /**
     * Get the result's server country.
     */
    protected function serverCountry(): Attribute
    {
        return Attribute::make(
            get: fn () => Arr::get($this->data, 'server.country'),
        );
    }
}
