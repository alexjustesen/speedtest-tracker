<?php

namespace Tests\Unit;

use App\Jobs\Speedtests\ExecuteOoklaSpeedtest;
use App\Models\Result;
use Tests\TestCase;

final class ExecuteOoklaSpeedtestTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_ping_urls_handled(): void
    {
        $job = new ExecuteOoklaSpeedtest(new Result);
        config(['speedtest' => ['ping_url' => 'google.com']]);

        $passingCases = [
            // ipv6
            '2a00:1450:4016:80c::200e',
            // ipv4
            '1.1.1.1',
            // hostname
            'google.com',
            // hostname with subdomain
            'www.google.com',
            // hostname with protocol
            'http://google.com',
            'https://google.com',
        ];

        foreach ($passingCases as $case) {
            $this->assertTrue($job->isValidPingUrl($case), "$case is an invalid ping url");
        }

        $failingCases = [
            // invalid hostname
            'google',
            // no tld
            'http://google',
            'https://google',
            // invalid ipv4
            '1.1.1',
            '1.1.1.1.',
            '1.1.1.1.1',
            '.1.1.1.1',
            // invalid ipv6
            '2a00:1450:4016:80c::200e::',
            '2a00:14504:4016:80c::200e',
            '2a00:1450:401680c::200e',
            '2v00:1450:4016:80c::200e',
            '2::1:1450:4016:80c::200e',
            // path included
            'https://google.com/test',
            // path and query included
            'https://google.com/test?query=1',
            // path, query and fragment included
            'https://www.google.com/test?query=1#fragment',
            // path, query, fragment and port included
            'https://google.com:8080/test?query=1#fragment',
        ];

        foreach ($failingCases as $case) {
            $this->assertFalse($job->isValidPingUrl($case), "$case is a valid ping url");
        }
    }
}
