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
            'status' => $this->status,
            'scheduled' => $this->scheduled,
            'healthy' => $this->healthy,
            'ping' => $this->ping,
            'ping_jitter' => $this->ping_jitter,
            'ping_low' => $this->ping_low,
            'ping_high' => $this->ping_high,
            'download' => $this->download,
            'download_bits' => $this->when($this->download, fn (): int|float => Bitrate::bytesToBits($this->download)),
            'download_bits_human' => $this->when($this->download, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->download)).'ps'),
            'download_bytes' => $this->download_bytes,
            'download_bytes_human' => $this->when($this->download_bytes, fn (): string => Number::fileSize($this->download_bytes)),
            'download_jitter' => $this->download_jitter,
            'download_latency_iqm' => $this->download_latency_iqm,
            'download_latency_low' => $this->download_latency_low,
            'download_latency_high' => $this->download_latency_high,
            'download_elapsed' => $this->download_elapsed,
            'upload' => $this->upload,
            'upload_bits' => $this->when($this->upload, fn (): int|float => Bitrate::bytesToBits($this->upload)),
            'upload_bits_human' => $this->when($this->upload, fn (): string => Bitrate::formatBits(Bitrate::bytesToBits($this->upload)).'ps'),
            'upload_bytes' => $this->upload_bytes,
            'upload_bytes_human' => $this->when($this->upload_bytes, fn (): string => Number::fileSize($this->upload_bytes)),
            'upload_jitter' => $this->upload_jitter,
            'upload_latency_iqm' => $this->upload_latency_iqm,
            'upload_latency_low' => $this->upload_latency_low,
            'upload_latency_high' => $this->upload_latency_high,
            'upload_elapsed' => $this->upload_elapsed,
            'packet_loss' => $this->packet_loss,
            'isp' => $this->isp,
            'ip_address' => $this->ip_address,
            'server_id' => $this->server_id,
            'server_name' => $this->server_name,
            'server_host' => $this->server_host,
            'server_ip' => $this->server_ip,
            'server_country' => $this->server_country,
            'server_location' => $this->server_location,
            'result_url' => $this->result_url,
            'error_message' => $this->error_message,
            'benchmarks' => $this->benchmarks,
            'dispatched_by' => $this->dispatched_by,
            'comments' => $this->comments,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
