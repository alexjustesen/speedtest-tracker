<?php

use App\Helpers\Nettest;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Builds a failed process that reports the given diagnostics on stderr.
 */
function failedProcess(string $errorOutput): Process
{
    $process = Mockery::mock(Process::class);
    $process->shouldReceive('isSuccessful')->andReturn(false);
    $process->shouldReceive('getCommandLine')->andReturn('nettest -c 192.168.1.100 -json');
    $process->shouldReceive('getExitCode')->andReturn(1);
    $process->shouldReceive('getExitCodeText')->andReturn('General error');
    $process->shouldReceive('getWorkingDirectory')->andReturn('/');
    $process->shouldReceive('isOutputDisabled')->andReturn(false);
    $process->shouldReceive('getOutput')->andReturn('');
    $process->shouldReceive('getErrorOutput')->andReturn($errorOutput);

    return $process;
}

describe('Nettest::getCommand', function () {
    it('builds the command with the configured server', function () {
        Config::set('speedtest.nettest', [
            'server' => '192.168.1.100',
            'port' => null,
            'threads' => null,
            'tls' => false,
            'websocket' => false,
        ]);

        expect(Nettest::getCommand())->toBe([
            'nettest',
            '-c',
            '192.168.1.100',
            '-json',
        ]);
    });

    it('passes the port, the threads and the protocol options when they are configured', function () {
        Config::set('speedtest.nettest', [
            'server' => 'nettest.example.com',
            'port' => 5005,
            'threads' => 4,
            'tls' => true,
            'websocket' => true,
        ]);

        expect(Nettest::getCommand())->toBe([
            'nettest',
            '-c',
            'nettest.example.com',
            '-p',
            '5005',
            '-t',
            '4',
            '-tls',
            '-ws',
            '-json',
        ]);
    });
});

describe('Nettest::mapResult', function () {
    it('maps a complete measurement onto the result columns and data', function () {
        $mapped = Nettest::mapResult([
            'type' => 'measurement',
            'ping' => ['latency_ms' => 12.34, 'jitter_ms' => 0.42],
            'download' => ['speed_bps' => 800, 'bytes_transferred' => 1000],
            'upload' => ['speed_bps' => 400, 'bytes_transferred' => 500],
            'packet_loss_percent' => 0.5,
            'server' => ['host' => '192.168.1.100', 'port' => 5005],
        ]);

        expect($mapped['ping'])->toBe(12.34);
        expect($mapped['download'])->toEqual(100);
        expect($mapped['upload'])->toEqual(50);
        expect($mapped['download_bytes'])->toBe(1000);
        expect($mapped['upload_bytes'])->toBe(500);
        expect($mapped['data']['ping'])->toBe(['latency' => 12.34, 'jitter' => 0.42]);
        expect($mapped['data']['packetLoss'])->toBe(0.5);
        expect($mapped['data']['server'])->toBe([
            'name' => '192.168.1.100',
            'host' => '192.168.1.100',
            'port' => 5005,
        ]);
    });

    it('keeps the values that nettest did not measure absent', function () {
        $mapped = Nettest::mapResult([
            'type' => 'measurement',
            'ping' => ['latency_ms' => 12.34],
            'download' => ['speed_bps' => 800],
            'upload' => ['speed_bps' => 400],
            'server' => ['host' => '192.168.1.100', 'port' => 5005],
        ]);

        expect($mapped['download_bytes'])->toBeNull();
        expect($mapped['upload_bytes'])->toBeNull();
        expect($mapped['data']['ping']['jitter'])->toBeNull();
        expect($mapped['data']['packetLoss'])->toBeNull();
    });

    it('rounds the speeds because the result columns hold whole bytes', function () {
        $mapped = Nettest::mapResult([
            'type' => 'measurement',
            'download' => ['speed_bps' => 208744585758],
            'upload' => ['speed_bps' => 176551252943],
        ]);

        expect($mapped['download'])->toBe(26093073220);
        expect($mapped['upload'])->toBe(22068906618);
    });

    it('stores the measurement as reported by nettest', function () {
        $output = [
            'type' => 'measurement',
            'client' => ['name' => 'nettest', 'version' => '2.1.0'],
            'ping' => ['latency_ms' => 12.34],
        ];

        expect(Nettest::mapResult($output)['data']['nettest'])->toBe($output);
    });
});

describe('Nettest::getErrorMessage', function () {
    it('returns the diagnostics that nettest reported on stderr', function () {
        $process = failedProcess("Could not connect to server\nCould not connect to server\n");

        $message = Nettest::getErrorMessage(new ProcessFailedException($process));

        expect($message)->toBe('Could not connect to server');
    });

    it('falls back to a generic message when nettest reported nothing', function () {
        $process = failedProcess('');

        $message = Nettest::getErrorMessage(new ProcessFailedException($process));

        expect($message)->toBe('An unexpected error occurred while running the Nettest CLI.');
    });
});
