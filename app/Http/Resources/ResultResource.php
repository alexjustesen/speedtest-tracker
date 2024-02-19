<?php

namespace App\Http\Resources;

use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    private mixed $id;
    private mixed $ping;
    private mixed $download;
    private mixed $upload;
    private mixed $server_id;
    private mixed $server_host;
    private mixed $server_name;
    private mixed $url;
    private mixed $scheduled;
    private mixed $successful;
    private mixed $created_at;

    /**
     * @param mixed $id
     * @param mixed $ping
     * @param mixed $download
     * @param mixed $upload
     * @param mixed $server_id
     * @param mixed $server_host
     * @param mixed $server_name
     * @param mixed $url
     * @param mixed $scheduled
     * @param mixed $successful
     * @param mixed $created_at
     */
    public function __construct(Result $result)
    {
        parent::__construct($result);
        $this->id = $result->id;
        $this->ping = $result->ping;
        $this->download = $result->download;
        $this->upload = $result->upload;
        $this->server_id = $result->server_id;
        $this->server_host = $result->server_host;
        $this->server_name = $result->server_name;
        $this->url = $result->url;
        $this->scheduled = $result->scheduled;
        $this->successful = $result->successful;
        $this->created_at = $result->created_at;
    }


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ping' => $this->ping,
            'download' => !blank($this->download) ? toBits(convertSize($this->download)) : null,
            'upload' => !blank($this->upload) ? toBits(convertSize($this->upload)) : null,
            'server_id' => $this->server_id,
            'server_host' => $this->server_host,
            'server_name' => $this->server_name,
            'url' => $this->url,
            'scheduled' => $this->scheduled,
            'failed' => !$this->successful,
            'created_at' => $this->created_at->toISOString(true),
            'updated_at' => $this->created_at->toISOString(true), // faking updated at to match legacy api payload
        ];
    }


}
