<?php

use App\Actions\Schedules\ResolveScheduleServerLabels;
use Illuminate\Support\Facades\Cache;

describe('ResolveScheduleServerLabels', function () {
    it('returns a label from cache when present', function () {
        Cache::put('ookla_server_label_12345', 'Test Server (New York)', now()->addHour());

        $result = ResolveScheduleServerLabels::run(
            servers: ['12345'],
            blockedServers: [],
        );

        expect($result)->toBe(['12345' => 'Test Server (New York)']);
    });

    it('omits the entry when no cache label exists', function () {
        Cache::forget('ookla_server_label_99999');

        $result = ResolveScheduleServerLabels::run(
            servers: ['99999'],
            blockedServers: [],
        );

        expect($result)->toBeEmpty();
    });

    it('merges servers and blocked_servers without duplicates', function () {
        Cache::put('ookla_server_label_111', 'Server A', now()->addHour());
        Cache::put('ookla_server_label_222', 'Server B', now()->addHour());

        $result = ResolveScheduleServerLabels::run(
            servers: ['111'],
            blockedServers: ['222'],
        );

        expect($result)->toBe(['111' => 'Server A', '222' => 'Server B']);
    });

    it('returns an empty array when both inputs are empty', function () {
        $result = ResolveScheduleServerLabels::run(
            servers: [],
            blockedServers: [],
        );

        expect($result)->toBeEmpty();
    });

    it('falls back to an existing label when the cache entry has expired', function () {
        Cache::forget('ookla_server_label_333');

        $result = ResolveScheduleServerLabels::run(
            servers: ['333'],
            blockedServers: [],
            existingLabels: ['333' => 'Server C'],
        );

        expect($result)->toBe(['333' => 'Server C']);
    });

    it('prefers the fresh cache label over an existing one', function () {
        Cache::put('ookla_server_label_444', 'Server D (fresh)', now()->addHour());

        $result = ResolveScheduleServerLabels::run(
            servers: ['444'],
            blockedServers: [],
            existingLabels: ['444' => 'Server D (stale)'],
        );

        expect($result)->toBe(['444' => 'Server D (fresh)']);
    });
});
