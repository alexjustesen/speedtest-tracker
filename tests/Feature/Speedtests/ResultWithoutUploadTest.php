<?php

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Results\Pages\ListResults;
use App\Models\Result;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    $this->result = Result::factory()->create([
        'service' => ResultService::File,
        'status' => ResultStatus::Completed,
        'ping' => 12.5,
        'download' => 12_500_000,
        'upload' => null,
        'download_bytes' => 125_000_000,
        'upload_bytes' => null,
        'data' => [
            'ping' => ['latency' => 12.5],
            'download' => ['bandwidth' => 12_500_000, 'bytes' => 125_000_000, 'elapsed' => 10_000],
            'server' => ['name' => 'speed.example.com', 'host' => 'speed.example.com'],
        ],
    ]);
});

test('the dashboard renders a latest result without upload', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('n/a');
});

test('the results table lists a result without upload', function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(ListResults::class)
        ->assertCanSeeTableRecords([$this->result])
        ->filterTable('service', [ResultService::File->value])
        ->assertCanSeeTableRecords([$this->result]);
});
