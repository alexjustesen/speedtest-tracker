<?php

use App\Enums\UserRole;
use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Models\Schedule;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
});

describe('list page', function () {
    it('can render the list page', function () {
        Livewire::test(ListSchedules::class)->assertOk();
    });

    it('can see schedule records in the table', function () {
        $schedules = Schedule::factory()->count(3)->create();

        Livewire::test(ListSchedules::class)
            ->assertCanSeeTableRecords($schedules);
    });

    it('renders the enabled toggle column reflecting model state', function () {
        $enabled = Schedule::factory()->create(['enabled' => true]);
        $disabled = Schedule::factory()->disabled()->create();

        Livewire::test(ListSchedules::class)
            ->assertTableColumnStateSet('enabled', true, record: $enabled)
            ->assertTableColumnStateSet('enabled', false, record: $disabled);
    });

    it('shows next run at for enabled schedules', function () {
        Schedule::factory()->create(['enabled' => true, 'schedule' => '* * * * *']);
        $disabled = Schedule::factory()->disabled()->create(['schedule' => '* * * * *']);

        $component = Livewire::test(ListSchedules::class);

        $component->assertTableColumnStateSet('next_run_at', null, record: $disabled);
    });

    it('filters to only enabled schedules', function () {
        $enabled = Schedule::factory()->create(['enabled' => true]);
        $disabled = Schedule::factory()->disabled()->create();

        Livewire::test(ListSchedules::class)
            ->filterTable('enabled', true)
            ->assertCanSeeTableRecords([$enabled])
            ->assertCanNotSeeTableRecords([$disabled]);
    });

    it('filters to only disabled schedules', function () {
        $enabled = Schedule::factory()->create(['enabled' => true]);
        $disabled = Schedule::factory()->disabled()->create();

        Livewire::test(ListSchedules::class)
            ->filterTable('enabled', false)
            ->assertCanSeeTableRecords([$disabled])
            ->assertCanNotSeeTableRecords([$enabled]);
    });

    it('can disable an enabled schedule from the action group', function () {
        $schedule = Schedule::factory()->create(['enabled' => true]);

        Livewire::test(ListSchedules::class)
            ->callAction(TestAction::make('toggleEnabled')->table($schedule));

        expect($schedule->fresh()->enabled)->toBeFalse();
    });

    it('can enable a disabled schedule from the action group', function () {
        $schedule = Schedule::factory()->disabled()->create();

        Livewire::test(ListSchedules::class)
            ->callAction(TestAction::make('toggleEnabled')->table($schedule));

        expect($schedule->fresh()->enabled)->toBeTrue();
    });

});

describe('create page', function () {
    it('can render the create page', function () {
        Livewire::test(CreateSchedule::class)->assertOk();
    });

    it('can create a schedule with preferred servers', function () {
        Livewire::test(CreateSchedule::class)
            ->fillForm([
                'name' => 'Hourly test',
                'enabled' => true,
                'schedule' => '0 * * * *',
                'server_mode' => 'prefer',
                'servers' => ['12345', '67890'],
                'interface' => 'eth0',
                'skip_ips' => ['1.2.3.4', '10.0.0.0/8'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $schedule = Schedule::query()->where('name', 'Hourly test')->first();

        expect($schedule)->not->toBeNull()
            ->and($schedule->enabled)->toBeTrue()
            ->and($schedule->schedule)->toBe('0 * * * *')
            ->and($schedule->servers)->toBe(['12345', '67890'])
            ->and($schedule->blocked_servers)->toBeNull()
            ->and($schedule->interface)->toBe('eth0')
            ->and($schedule->skip_ips)->toBe(['1.2.3.4', '10.0.0.0/8']);
    });

    it('can create a schedule with blocked servers', function () {
        Livewire::test(CreateSchedule::class)
            ->fillForm([
                'name' => 'Block test',
                'enabled' => true,
                'schedule' => '0 * * * *',
                'server_mode' => 'block',
                'blocked_servers' => ['99999'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $schedule = Schedule::query()->where('name', 'Block test')->first();

        expect($schedule->servers)->toBeNull()
            ->and($schedule->blocked_servers)->toBe(['99999']);
    });

    it('stores null when servers is left empty', function () {
        Livewire::test(CreateSchedule::class)
            ->fillForm([
                'name' => 'No servers',
                'enabled' => true,
                'schedule' => '*/30 * * * *',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $schedule = Schedule::query()->where('name', 'No servers')->first();

        expect($schedule->servers)->toBeNull()
            ->and($schedule->blocked_servers)->toBeNull()
            ->and($schedule->interface)->toBeNull()
            ->and($schedule->skip_ips)->toBeNull();
    });

    it('requires a name', function () {
        Livewire::test(CreateSchedule::class)
            ->fillForm(['name' => null, 'schedule' => '0 * * * *'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('requires a cron schedule', function () {
        Livewire::test(CreateSchedule::class)
            ->fillForm(['name' => 'Test', 'schedule' => null])
            ->call('create')
            ->assertHasFormErrors(['schedule' => 'required']);
    });

    it('rejects a duplicate cron expression', function () {
        Schedule::factory()->create(['schedule' => '0 * * * *']);

        Livewire::test(CreateSchedule::class)
            ->fillForm(['name' => 'Duplicate', 'schedule' => '0 * * * *'])
            ->call('create')
            ->assertHasFormErrors(['schedule' => 'unique']);
    });

});

describe('edit page', function () {
    it('can render the edit page', function () {
        $schedule = Schedule::factory()->create();

        Livewire::test(EditSchedule::class, ['record' => $schedule->id])->assertOk();
    });

    it('sets server_mode to prefer when servers are configured', function () {
        $schedule = Schedule::factory()->create([
            'servers' => ['12345', '67890'],
            'blocked_servers' => null,
        ]);

        Livewire::test(EditSchedule::class, ['record' => $schedule->id])
            ->assertFormSet([
                'server_mode' => 'prefer',
                'servers' => ['12345', '67890'],
            ]);
    });

    it('sets server_mode to block when blocked servers are configured', function () {
        $schedule = Schedule::factory()->create([
            'servers' => null,
            'blocked_servers' => ['99999'],
        ]);

        Livewire::test(EditSchedule::class, ['record' => $schedule->id])
            ->assertFormSet([
                'server_mode' => 'block',
                'blocked_servers' => ['99999'],
            ]);
    });

    it('can update a schedule', function () {
        $schedule = Schedule::factory()->create(['name' => 'Old name', 'enabled' => true]);

        Livewire::test(EditSchedule::class, ['record' => $schedule->id])
            ->fillForm([
                'name' => 'New name',
                'enabled' => false,
                'schedule' => '*/15 * * * *',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($schedule->fresh())
            ->name->toBe('New name')
            ->enabled->toBeFalse()
            ->schedule->toBe('*/15 * * * *');
    });

    it('allows saving own cron expression unchanged', function () {
        $schedule = Schedule::factory()->create(['schedule' => '0 * * * *']);

        Livewire::test(EditSchedule::class, ['record' => $schedule->id])
            ->fillForm(['schedule' => '0 * * * *'])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects changing to a cron expression used by another schedule', function () {
        Schedule::factory()->create(['schedule' => '0 * * * *']);
        $schedule = Schedule::factory()->create(['schedule' => '*/5 * * * *']);

        Livewire::test(EditSchedule::class, ['record' => $schedule->id])
            ->fillForm(['schedule' => '0 * * * *'])
            ->call('save')
            ->assertHasFormErrors(['schedule' => 'unique']);
    });

    it('can delete a schedule', function () {
        $schedule = Schedule::factory()->create();

        Livewire::test(EditSchedule::class, ['record' => $schedule->id])
            ->callAction('delete')
            ->assertRedirect();

        expect(Schedule::find($schedule->id))->toBeNull();
    });
});
