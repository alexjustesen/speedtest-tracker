<?php

use App\Actions\PingHostname;
use App\Services\Speedtests\Iperf3Speedtest;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Spatie\Ping\PingResult;

function iperf3Output(int $bytes, float $seconds, int $meanRtt = 0): string
{
    return json_encode([
        'start' => ['connected' => [['remote_host' => '192.0.2.10', 'remote_port' => 5201]]],
        'end' => [
            'streams' => [
                ['sender' => ['mean_rtt' => $meanRtt, 'min_rtt' => intdiv($meanRtt, 2), 'max_rtt' => $meanRtt * 2]],
                ['sender' => ['mean_rtt' => $meanRtt, 'min_rtt' => intdiv($meanRtt, 2), 'max_rtt' => $meanRtt * 2]],
            ],
            'sum_received' => [
                'bytes' => $bytes,
                'seconds' => $seconds,
                'bits_per_second' => $bytes * 8 / $seconds,
            ],
        ],
    ]);
}

function fakePing(?PingResult $ping): void
{
    app()->bind(PingHostname::class, fn () => new class($ping)
    {
        public function __construct(private ?PingResult $ping) {}

        public function handle(?string $hostname = null, int $count = 1): ?PingResult
        {
            return $this->ping;
        }
    });
}

beforeEach(function () {
    Http::fake(['*' => Http::response('203.0.113.5')]);
    Sleep::fake();
});

test('targets are split into host and port', function (string $target, string $host, int $port) {
    expect(Iperf3Speedtest::parseTarget($target))->toBe([$host, $port]);
})->with([
    ['iperf.example.com', 'iperf.example.com', 5201],
    [' iperf.example.com:5209 ', 'iperf.example.com', 5209],
    ['192.0.2.10:5002', '192.0.2.10', 5002],
    ['[2001:db8::1]:5201', '2001:db8::1', 5201],
    ['2001:db8::1', '2001:db8::1', 5201],
]);

test('invalid targets are rejected', function (?string $target) {
    Iperf3Speedtest::parseTarget($target);
})->with([
    [null],
    [''],
    ['iperf.example.com:abc'],
    ['iperf.example.com:70000'],
    ['--help'],
    ['host name'],
])->throws(RuntimeException::class);

test('download and upload are measured with the iperf3 CLI', function () {
    fakePing(PingResult::fromArray([
        'success' => true,
        'host' => 'iperf.example.com',
        'packet_loss_percentage' => 20,
        'timings' => [
            'minimum_time_in_ms' => 9.5,
            'maximum_time_in_ms' => 14.1,
            'average_time_in_ms' => 11.2,
            'standard_deviation_time_in_ms' => 1.3,
        ],
    ]));

    Process::fake(fn (PendingProcess $process) => in_array('--reverse', $process->command)
        ? Process::result(iperf3Output(125_000_000, 10))
        : Process::result(iperf3Output(50_000_000, 10, 20_000)));

    $output = (new Iperf3Speedtest)->run('iperf.example.com:5202');

    expect($output['ping'])->toBe(['latency' => 11.2, 'jitter' => 1.3, 'low' => 9.5, 'high' => 14.1])
        ->and($output['download'])->toBe(['bandwidth' => 12_500_000, 'bytes' => 125_000_000, 'elapsed' => 10_000])
        ->and($output['upload'])->toBe([
            'bandwidth' => 5_000_000,
            'bytes' => 50_000_000,
            'elapsed' => 10_000,
            'latency' => ['iqm' => 20.0, 'low' => 10.0, 'high' => 40.0],
        ])
        ->and($output['packetLoss'])->toBe(20)
        ->and($output['server'])->toBe(['name' => 'iperf.example.com', 'host' => 'iperf.example.com', 'port' => 5202, 'ip' => '192.0.2.10'])
        ->and($output['interface']['externalIp'])->toBe('203.0.113.5');

    Process::assertRan(fn (PendingProcess $process): bool => $process->command === [
        'iperf3', '--client', 'iperf.example.com', '--port', '5202', '--json', '--time', '10',
        '--parallel', '4', '--omit', '1', '--connect-timeout', '5000', '--reverse',
    ]);
});

test('the TCP round trip time is used when ICMP is not available', function () {
    fakePing(null);

    Process::fake(['*' => Process::result(iperf3Output(50_000_000, 10, 20_000))]);

    $output = (new Iperf3Speedtest)->run('iperf.example.com');

    expect($output['ping'])->toBe(['latency' => 10.0, 'low' => 10.0])
        ->and($output)->not->toHaveKey('packetLoss');
});

test('a busy server is retried', function () {
    fakePing(null);

    Process::fake(['*' => Process::sequence()
        ->push(Process::result(json_encode(['error' => 'the server is busy running a test. try again later']), exitCode: 1))
        ->push(Process::result(iperf3Output(125_000_000, 10)))
        ->push(Process::result(iperf3Output(50_000_000, 10))),
    ]);

    $output = (new Iperf3Speedtest)->run('iperf.example.com');

    expect($output['download']['bandwidth'])->toBe(12_500_000);
    Process::assertRanTimes(fn (PendingProcess $process): bool => in_array('--reverse', $process->command), 2);
    Sleep::assertSleptTimes(1);
});

test('iperf3 errors are reported', function () {
    fakePing(null);

    Process::fake(['*' => Process::result(json_encode(['error' => 'unable to connect to server: Connection refused']), exitCode: 1)]);

    (new Iperf3Speedtest)->run('iperf.example.com');
})->throws(RuntimeException::class, 'iPerf3 failed: unable to connect to server: Connection refused');
