<?php

use App\Enums\ResultStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Results\Pages\ListResults;
use App\Models\Result;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('filters results by a numeric server id stored in the JSON data column', function () {
    $matching = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'data' => ['server' => ['id' => 16072, 'name' => 'Hazelton, PA']],
    ]);

    $other = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'data' => ['server' => ['id' => 16071, 'name' => 'Birdsboro, PA']],
    ]);

    Livewire::test(ListResults::class)
        ->filterTable('server_id', ['16072'])
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});
