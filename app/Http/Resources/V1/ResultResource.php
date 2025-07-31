<?php

namespace App\Http\Resources\V1;

use App\Helpers\Bitrate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => $this->service,
            'ping' => $this->ping,
            'download' => $this->download,
            'upload' => $this->upload,
            'download_bits' => $this->when($this->download, fn (): int|float => Bitrate::bytesToBits($this->download)),
            'upload_bits' => $this->when($this->upload, fn (): int|float => Bitrate::bytesToBits($this->upload)),
            'download_bits_human' => $this->when($this->download, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->download)).'ps'),
            'upload_bits_human' => $this->when($this->upload, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->upload)).'ps'),
            'download_bytes' => $this->download_bytes,
            'upload_bytes' => $this->upload_bytes,
            'download_bytes_human' => $this->when($this->download_bytes, fn (): string => Number::fileSize($this->download_bytes)),
            'upload_bytes_human' => $this->when($this->upload_bytes, fn (): string => Number::fileSize($this->upload_bytes)),
            'benchmarks' => $this->benchmarks,
            'healthy' => $this->healthy,
            'status' => $this->status,
            'scheduled' => $this->scheduled,
            'comments' => $this->comments,
            'data' => $this->data,
            'created_at' => $this->created_at->toDateTimestring(),
            'updated_at' => $this->updated_at->toDateTimestring(),
        ];
    }
}
