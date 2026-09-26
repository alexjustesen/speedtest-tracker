<?php

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

        $component->assertTableColumnStateSet('next_run_at', null, record: $disabled);
    });

    it('shows last ran at for schedules that have fired, and a placeholder for schedules that have not', function () {
        $neverRun = Speedtest::factory()->create();

        $ranBefore = Speedtest::factory()->create();
        $lastRunAt = now()->subHour()->startOfSecond();
        $ranBefore->cronSchedule()->forceFill(['last_run_at' => $lastRunAt])->save();

        Livewire::test(ListSpeedtests::class)
            ->assertTableColumnStateSet('last_run_at', null, record: $neverRun)
            ->assertTableColumnStateSet('last_run_at', $lastRunAt, record: $ranBefore);
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

        expect($speedtest->fresh()->isEnabled)->toBeFalse();
    });

    it('can enable a disabled schedule from the action group', function () {
        $speedtest = Speedtest::factory()->disabled()->create();

        Livewire::test(ListSpeedtests::class)
            ->callAction(TestAction::make('toggleEnabled')->table($speedtest));

        expect($speedtest->fresh()->isEnabled)->toBeTrue();
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
                'schedule' => '0 * * * *',
                'server_mode' => 'prefer',
                'servers' => ['12345', '67890'],
                'interface' => 'eth0',
                'skip_ips' => ['1.2.3.4', '10.0.0.0/8'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $speedtest = Speedtest::query()->where('name', 'Hourly test')->first();

        expect($speedtest)->not->toBeNull()
            ->and($speedtest->isEnabled)->toBeTrue()
            ->and($speedtest->cronExpression)->toBe('0 * * * *')
            ->and($speedtest->servers)->toBe(['12345', '67890'])
            ->and($speedtest->blocked_servers)->toBeNull()
            ->and($speedtest->interface)->toBe('eth0')
            ->and($speedtest->skip_ips)->toBe(['1.2.3.4', '10.0.0.0/8']);

        $this->assertDatabaseHas('schedules', [
            'schedulable_type' => Speedtest::class,
            'schedulable_id' => $speedtest->id,
            'expression' => '0 * * * *',
        ]);
    });

    it('can create a speedtest with blocked servers', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm([
                'name' => 'Block test',
                'enabled' => true,
                'schedule' => '0 * * * *',
                'server_mode' => 'block',
                'blocked_servers' => ['99999'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $speedtest = Speedtest::query()->where('name', 'Block test')->first();

        expect($speedtest->servers)->toBeNull()
            ->and($speedtest->blocked_servers)->toBe(['99999']);
    });

    it('stores null when servers is left empty', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm([
                'name' => 'No servers',
                'enabled' => true,
                'schedule' => '*/30 * * * *',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $speedtest = Speedtest::query()->where('name', 'No servers')->first();

        expect($speedtest->servers)->toBeNull()
            ->and($speedtest->blocked_servers)->toBeNull()
            ->and($speedtest->interface)->toBeNull()
            ->and($speedtest->skip_ips)->toBeNull();
    });

    it('requires a name', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm(['name' => null, 'schedule' => '0 * * * *'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('requires a cron schedule', function () {
        Livewire::test(CreateSpeedtest::class)
            ->fillForm(['name' => 'Test', 'schedule' => null])
            ->call('create')
            ->assertHasFormErrors(['schedule' => 'required']);
    });

    it('rejects a duplicate cron expression', function () {
        Speedtest::factory()->withCron('0 * * * *')->create();

        Livewire::test(CreateSpeedtest::class)
            ->fillForm(['name' => 'Duplicate', 'schedule' => '0 * * * *'])
            ->call('create')
            ->assertHasFormErrors(['schedule' => 'unique']);
    });

});

describe('edit page', function () {
    it('can render the edit page', function () {
        $speedtest = Speedtest::factory()->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])->assertOk();
    });

    it('sets server_mode to prefer when servers are configured', function () {
        $speedtest = Speedtest::factory()->create([
            'servers' => ['12345', '67890'],
            'blocked_servers' => null,
        ]);

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->assertFormSet([
                'server_mode' => 'prefer',
                'servers' => ['12345', '67890'],
            ]);
    });

    it('sets server_mode to block when blocked servers are configured', function () {
        $speedtest = Speedtest::factory()->create([
            'servers' => null,
            'blocked_servers' => ['99999'],
        ]);

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->assertFormSet([
                'server_mode' => 'block',
                'blocked_servers' => ['99999'],
            ]);
    });

    it('can update a speedtest', function () {
        $speedtest = Speedtest::factory()->create(['name' => 'Old name']);

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->fillForm([
                'name' => 'New name',
                'enabled' => false,
                'schedule' => '*/15 * * * *',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($speedtest->fresh())
            ->name->toBe('New name')
            ->isEnabled->toBeFalse()
            ->cronExpression->toBe('*/15 * * * *');
    });

    it('allows saving own cron expression unchanged', function () {
        $speedtest = Speedtest::factory()->withCron('0 * * * *')->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->fillForm(['schedule' => '0 * * * *'])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects changing to a cron expression used by another schedule', function () {
        Speedtest::factory()->withCron('0 * * * *')->create();
        $speedtest = Speedtest::factory()->withCron('*/5 * * * *')->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->fillForm(['schedule' => '0 * * * *'])
            ->call('save')
            ->assertHasFormErrors(['schedule' => 'unique']);
    });

    it('can delete a speedtest', function () {
        $speedtest = Speedtest::factory()->create();

        Livewire::test(EditSpeedtest::class, ['record' => $speedtest->id])
            ->callAction('delete')
            ->assertRedirect();

        expect(Speedtest::find($speedtest->id))->toBeNull();
    });
});
