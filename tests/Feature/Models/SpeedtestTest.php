<?php

use App\Models\Speedtest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

describe('Speedtest', function () {
    it('resolves server labels from the cache when servers change', function () {
        Cache::put('ookla_server_label_111', 'Server A (City, 111)', now()->addHour());

        $speedtest = Speedtest::factory()->prefer(['111', '222'])->create();

        expect($speedtest->server_labels)->toBe(['111' => 'Server A (City, 111)'])
            ->and($speedtest->serverLabels())->toBe(['111' => 'Server A (City, 111)', '222' => '222']);
    });

    it('keeps existing labels when the cache has expired', function () {
        Cache::put('ookla_server_label_111', 'Server A (City, 111)', now()->addHour());
        $speedtest = Speedtest::factory()->prefer(['111'])->create();
        Cache::flush();

        $speedtest->update(['servers' => ['111', '222']]);

        expect($speedtest->server_labels)->toBe(['111' => 'Server A (City, 111)']);
    });

    it('deletes its schedule when deleted', function () {
        $speedtest = Speedtest::factory()->create();

        $speedtest->delete();

        expect(DB::table('schedules')->count())->toBe(0);
    });

    it('stores next_run_at in the app timezone when the schedule uses a display timezone', function () {
        config()->set('app.display_timezone', 'Europe/Amsterdam');
        $this->travelTo(Carbon::parse('2026-10-19 10:30:00', 'UTC'));

        $speedtest = Speedtest::factory()->withCron('0 9 * * *')->create();

        // 09:00 in Amsterdam (CEST, UTC+2) is 07:00 UTC.
        expect(DB::table('schedules')->where('schedulable_id', $speedtest->id)->value('next_run_at'))
            ->toBe('2026-10-20 07:00:00');

        $speedtest->schedule->advance(now());

        expect($speedtest->schedule->fresh()->next_run_at->toDateTimeString())->toBe('2026-10-20 07:00:00');
    });
});
