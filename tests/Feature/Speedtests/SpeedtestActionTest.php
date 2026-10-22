<?php

use App\Enums\ResultService;
use App\Enums\UserRole;
use App\Livewire\Topbar\Actions;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    Bus::fake();
    Http::fake();

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
});

test('a speedtest can be started with an alternative service', function () {
    Livewire::test(Actions::class)
        ->callAction('speedtest', data: ['service' => 'cloudflare'])
        ->assertHasNoActionErrors();

    expect(Result::sole()->service)->toBe(ResultService::Cloudflare);
});

test('the file download needs a valid URL', function () {
    Livewire::test(Actions::class)
        ->callAction('speedtest', data: ['service' => 'file', 'target' => 'not a url'])
        ->assertHasActionErrors(['target']);

    expect(Result::count())->toBe(0);
});

test('the iperf3 server is validated', function () {
    Livewire::test(Actions::class)
        ->callAction('speedtest', data: ['service' => 'iperf3', 'target' => 'iperf.example.com:abc'])
        ->assertHasActionErrors(['target']);

    Livewire::test(Actions::class)
        ->callAction('speedtest', data: ['service' => 'iperf3', 'target' => 'iperf.example.com:5201'])
        ->assertHasNoActionErrors();

    expect(Result::sole())
        ->service->toBe(ResultService::Iperf3)
        ->server_host->toBe('iperf.example.com:5201');
});
