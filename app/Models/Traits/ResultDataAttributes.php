<?php

namespace App\Models\Traits;

use App\Helpers\Bitrate;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
     * Get the result's upload in bits.
     */
    protected function uploadBits(): Attribute
    {
        return Attribute::make(
            get: fn (): null|int|float => ! blank($this->upload) ? Bitrate::bytesToBits($this->upload) : null,
        );
    }
}
