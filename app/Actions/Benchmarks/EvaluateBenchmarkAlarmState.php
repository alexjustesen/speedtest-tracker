<?php

namespace App\Actions\Benchmarks;

use App\Enums\BenchmarkState;
use App\Models\Benchmark;
use App\Models\Result;
use Lorisleiva\Actions\Concerns\AsAction;

class EvaluateBenchmarkAlarmState
{
    use AsAction;

    /**
     * Evaluate the benchmark's last `consecutive_breaches` scheduled results
     * against the result. By default this only reports on state transitions
     * (edge-triggered): once when the benchmark enters alarm, once when it
     * recovers. If the benchmark has `repeat_while_in_alarm` enabled, it
     * also reports on every subsequent breaching result while still in
     * alarm, without treating those repeats as a fresh transition.
     *
     * @return BenchmarkState|null The state to notify for, or null if nothing should be notified.
     */
    public function handle(Benchmark $benchmark, Result $result): ?BenchmarkState
    {
        // Manual "Run Now" tests never advance or trigger the alarm state.
        if ($result->unscheduled) {
            return null;
        }

        $window = Result::query()
            ->where('scheduled', true)
            ->where('id', '<=', $result->id)
            ->whereNotNull('benchmarks')
            ->orderByDesc('id')
            ->limit($benchmark->consecutive_breaches)
            ->get();

        // Not enough history yet to require consecutive breaches from.
        if ($window->count() < $benchmark->consecutive_breaches) {
            return null;
        }

        $newState = $window->every(fn (Result $r): bool => ! $benchmark->passes($r))
            ? BenchmarkState::Alarm
            : BenchmarkState::Ok;

        $isTransition = $newState !== $benchmark->state;

        if (! $isTransition) {
            $repeating = $newState === BenchmarkState::Alarm && $benchmark->repeat_while_in_alarm;

            if (! $repeating) {
                return null;
            }
        } else {
            $benchmark->update([
                'state' => $newState,
                'state_changed_at' => now(),
            ]);
        }

        return $newState;
    }
}
