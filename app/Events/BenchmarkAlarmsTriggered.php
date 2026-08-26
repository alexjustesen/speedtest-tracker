<?php

namespace App\Events;

use App\Models\Benchmark;
use App\Models\Result;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BenchmarkAlarmsTriggered
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Collection<int, Benchmark>  $benchmarks
     */
    public function __construct(
        public Result $result,
        public Collection $benchmarks,
    ) {}
}
