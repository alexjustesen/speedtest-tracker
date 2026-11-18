<?php

use App\Enums\ServerMode;
use App\Enums\UserRole;
use App\Filament\Resources\Speedtests\Pages\CreateSpeedtest;
use App\Filament\Resources\Speedtests\Pages\EditSpeedtest;
use App\Filament\Resources\Speedtests\Pages\ListSpeedtests;
use App\Models\Speedtest;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
});

describe('list page', function () {
    it('can render the list page', function () {
        Livewire::test(ListSpeedtests::class)->assertOk();
    });

    it('can see speedtest records in the table', function () {
        $speedtests = Speedtest::factory()->count(3)->create();

        Livewire::test(ListSpeedtests::class)
            ->assertCanSeeTableRecords($speedtests);
    });

    it('renders the enabled toggle column reflecting model state', function () {
        $enabled = Speedtest::factory()->create();
        $disabled = Speedtest::factory()->disabled()->create();

        Livewire::test(ListSpeedtests::class)
            ->assertTableColumnStateSet('is_enabled', true, record: $enabled)
            ->assertTableColumnStateSet('is_enabled', false, record: $disabled);
    });

    it('shows next run at for enabled schedules', function () {
        Speedtest::factory()->withCron('* * * * *')->create();
        $disabled = Speedtest::factory()->withCron('* * * * *')->disabled()->create();

        $component = Livewire::test(ListSpeedtests::class);

        $component->assertTableColumnStateSet('schedule.next_run_at', null, record: $disabled);
    });

    it('shows last ran at for schedules that have fired, and a placeholder for schedules that have not', function () {
        $neverRun = Speedtest::factory()->create();

        $ranBefore = Speedtest::factory()->create();
        $lastRunAt = now()->subHour()->startOfSecond();
        $ranBefore->schedule->forceFill(['last_run_at' => $lastRunAt])->save();

        Livewire::test(ListSpeedtests::class)
            ->assertTableColumnStateSet('schedule.last_run_at', null, record: $neverRun)
            ->assertTableColumnStateSet('schedule.last_run_at', $lastRunAt, record: $ranBefore);
    });

    it('filters to only enabled schedules', function () {
        $enabled = Speedtest::factory()->create();
        $disabled = Speedtest::factory()->disabled()->create();

        Livewire::test(ListSpeedtests::class)
            ->filterTable('enabled', true)
            ->assertCanSeeTableRecords([$enabled])
            ->assertCanNotSeeTableRecords([$disabled]);
    });

    it('filters to only disabled schedules', function () {
        $enabled = Speedtest::factory()->create();
        $disabled = Speedtest::factory()->disabled()->create();

        Livewire::test(ListSpeedtests::class)
            ->filterTable('enabled', false)
            ->assertCanSeeTableRecords([$disabled])
            ->assertCanNotSeeTableRecords([$enabled]);
    });

    it('can disable an enabled schedule from the action group', function () {
        $speedtest = Speedtest::factory()->create();

        Livewire::test(ListSpeedtests::class)
            ->callAction(TestAction::make('toggleEnabled')->table($speedtest));

        expect($speedtest->fresh()->schedule->isEnabled())->toBeFalse();
    });

    it('can enable a disabled schedule from the action group', function () {
        $speedtest = Speedtest::factory()->disabled()->create();

        Livewire::test(ListSpeedtests::class)
            ->callAction(TestAction::make('toggleEnabled')->table($speedtest));

        expect($speedtest->fresh()->schedule->isEnabled())->toBeTrue();
    });

});

describe('create page', function () {
    it('can render the create page', function () {
        Livewire::test(CreateSpeedtest::class)->assertOk();
    });

    it('can create a speedtest with preferred servers', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm([
                'name' => 'Hourly test',
                'enabled' => true,
                'schedule' => ['expression' => '0 * * * *'],
                'server_mode' => 'prefer',
                'servers' => ['12345', '67890'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $speedtest = Speedtest::query()->where('name', 'Hourly test')->first();

        expect($speedtest)->not->toBeNull()
            ->and($speedtest->schedule->isEnabled())->toBeTrue()
            ->and($speedtest->schedule->expression)->toBe('0 * * * *')
            ->and($speedtest->schedule->next_run_at)->not->toBeNull()
            ->and($speedtest->server_mode)->toBe(ServerMode::Prefer)
            ->and($speedtest->servers)->toBe(['12345', '67890']);

        $this->assertDatabaseHas('schedules', [
            'schedulable_type' => Speedtest::class,
            'schedulable_id' => $speedtest->id,
            'expression' => '0 * * * *',
        ]);
    });

    it('stores null when servers is left empty', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm([
                'name' => 'No servers',
                'enabled' => true,
                'schedule' => ['expression' => '*/30 * * * *'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $speedtest = Speedtest::query()->where('name', 'No servers')->first();

        expect($speedtest->server_mode)->toBe(ServerMode::Auto)
            ->and($speedtest->servers)->toBeNull();
    });

    it('requires a name', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm(['name' => null, 'schedule.expression' => '0 * * * *'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('requires a cron schedule', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm(['name' => 'Test', 'schedule.expression' => null])
            ->call('create')
            ->assertHasFormErrors(['schedule.expression' => 'required']);
    });

    it('rejects a duplicate cron expression', function () {
        Speedtest::factory()->withCron('0 * * * *')->create();

        Livewire::test(CreateSpeedtest::class)
            ->fillForm(['name' => 'Duplicate', 'schedule.expression' => '0 * * * *'])
            ->call('create')
            ->assertHasFormErrors(['schedule.expression' => 'unique']);
    });

});

describe('edit page', function () {
    it('can render the edit page', function () {
        $speedtest = Speedtest::factory()->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])->assertOk();
    });

    it('sets server_mode to prefer when servers are configured', function () {
        $speedtest = Speedtest::factory()->prefer(['12345', '67890'])->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->assertFormSet([
                'server_mode' => ServerMode::Prefer,
                'servers' => ['12345', '67890'],
            ]);
    });

    it('can update a speedtest', function () {
        $speedtest = Speedtest::factory()->create(['name' => 'Old name']);

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->fillForm([
                'name' => 'New name',
                'enabled' => false,
                'schedule' => ['expression' => '*/15 * * * *'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $speedtest->refresh();

        expect($speedtest->name)->toBe('New name')
            ->and($speedtest->schedule->isEnabled())->toBeFalse()
            ->and($speedtest->schedule->expression)->toBe('*/15 * * * *')
            ->and($speedtest->schedule->next_run_at)->toBeNull()
            ->and($speedtest->schedules()->count())->toBe(1);
    });

    it('fills the enabled toggle from the schedule and can re-enable it', function () {
        $speedtest = Speedtest::factory()->withCron('0 * * * *')->disabled()->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->assertFormSet(['enabled' => false, 'schedule.expression' => '0 * * * *'])
            ->fillForm(['enabled' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($speedtest->fresh()->schedule)
            ->isEnabled()->toBeTrue()
            ->next_run_at->not->toBeNull();
    });

    it('allows saving own cron expression unchanged', function () {
        $speedtest = Speedtest::factory()->withCron('0 * * * *')->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->fillForm(['schedule.expression' => '0 * * * *'])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects changing to a cron expression used by another schedule', function () {
        Speedtest::factory()->withCron('0 * * * *')->create();
        $speedtest = Speedtest::factory()->withCron('*/5 * * * *')->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->fillForm(['schedule.expression' => '0 * * * *'])
            ->call('save')
            ->assertHasFormErrors(['schedule.expression' => 'unique']);
    });

    it('can delete a speedtest', function () {
        $speedtest = Speedtest::factory()->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->callAction('delete')
            ->assertRedirect();

        expect(Speedtest::find($speedtest->id))->toBeNull();
    });
});
