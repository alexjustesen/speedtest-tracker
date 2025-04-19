<?php

namespace App\Http\Resources\V1;

use App\Helpers\Bitrate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'downloaded_bytes' => $this->downloaded_bytes,
            'upload' => $this->upload,
            'uploaded_bytes' => $this->uploaded_bytes,
            'download_bits' => $this->when($this->download, fn (): int|float => Bitrate::bytesToBits($this->download)),
            'upload_bits' => $this->when($this->upload, fn (): int|float => Bitrate::bytesToBits($this->upload)),
            'download_bits_human' => $this->when($this->download, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->download)).'ps'),
            'upload_bits_human' => $this->when($this->upload, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->upload)).'ps'),
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
