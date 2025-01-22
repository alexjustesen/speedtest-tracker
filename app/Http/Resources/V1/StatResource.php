<?php

namespace App\Http\Resources\V1;

use App\Helpers\Bitrate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ping' => [
                'avg' => round($this->avg_ping, 2),
                'min' => round($this->min_ping, 2),
                'max' => round($this->max_ping, 2),
            ],
            'download' => [
                'avg' => round($this->avg_download),
                'avg_bits' => $this->when($this->avg_download, fn (): int|float => Bitrate::bytesToBits($this->avg_download)),
                'avg_bits_human' => $this->when($this->avg_download, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->avg_download)).'ps'),
                'min' => round($this->min_download),
                'min_bits' => $this->when($this->min_download, fn (): int|float => Bitrate::bytesToBits($this->min_download)),
                'min_bits_human' => $this->when($this->min_download, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->min_download)).'ps'),
                'max' => round($this->max_download),
                'max_bits' => $this->when($this->max_download, fn (): int|float => Bitrate::bytesToBits($this->max_download)),
                'max_bits_human' => $this->when($this->max_download, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->max_download)).'ps'),
            ],
            'upload' => [
                'avg' => round($this->avg_upload),
                'avg_bits' => $this->when($this->avg_upload, fn (): int|float => Bitrate::bytesToBits($this->avg_upload)),
                'avg_bits_human' => $this->when($this->avg_upload, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->avg_upload)).'ps'),
                'min' => round($this->min_upload),
                'min_bits' => $this->when($this->min_upload, fn (): int|float => Bitrate::bytesToBits($this->min_upload)),
                'min_bits_human' => $this->when($this->min_upload, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->min_upload)).'ps'),
                'max' => round($this->max_upload),
                'max_bits' => $this->when($this->max_upload, fn (): int|float => Bitrate::bytesToBits($this->max_upload)),
                'max_bits_human' => $this->when($this->max_upload, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->max_upload)).'ps'),
            ],
            'total_results' => $this->total_results,
        ];
    }
}
