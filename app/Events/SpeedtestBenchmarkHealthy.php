<?php

namespace App\Events;

use App\Models\Result;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpeedtestBenchmarkHealthy
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Result $result,
    ) {}
}
