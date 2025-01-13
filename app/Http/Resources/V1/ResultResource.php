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
            'download_bits' => $this->download ? Bitrate::bytesToBits($this->download) : null,
            'upload' => $this->upload,
            'upload_bits' => $this->upload ? Bitrate::bytesToBits($this->upload) : null,
            'data' => $this->data,
            'benchmarks' => $this->benchmarks,
            'healthy' => $this->healthy,
            'status' => $this->status,
            'scheduled' => $this->scheduled,
            'comments' => $this->comments,
            'created_at' => $this->created_at->toDateTimestring(),
            'updated_at' => $this->updated_at->toDateTimestring(),
        ];
    }
}
