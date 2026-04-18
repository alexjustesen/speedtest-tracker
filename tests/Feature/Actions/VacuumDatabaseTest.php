<?php

use App\Actions\VacuumDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

describe('VacuumDatabase', function () {
    it('skips silently when the default connection is not sqlite', function () {
        $connection = Mockery::mock();
        $connection->shouldReceive('getDriverName')->andReturn('mysql');
        $connection->shouldNotReceive('transactionLevel');
        $connection->shouldNotReceive('statement');
        DB::shouldReceive('connection')->andReturn($connection);

        Log::spy();

        VacuumDatabase::run();

        Log::shouldNotHaveReceived('info');
        Log::shouldNotHaveReceived('warning');
    });

    it('runs PRAGMA optimize and VACUUM on a sqlite connection', function () {
        Config::set('database.connections.sqlite_vacuum_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        Config::set('database.default', 'sqlite_vacuum_test');
        DB::purge();

        $statements = [];
        DB::listen(function ($query) use (&$statements) {
            $statements[] = $query->sql;
        });

        Log::spy();

        VacuumDatabase::run();

        expect($statements)->toContain('PRAGMA optimize;');
        expect($statements)->toContain('VACUUM;');
        Log::shouldHaveReceived('info')
            ->with('SQLite maintenance completed', Mockery::type('array'))
            ->once();
    });
});
