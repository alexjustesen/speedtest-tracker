<?php

use App\Services\Speedtests\YandexSpeedtest;

function yandexProbes(): array
{
    $base = 'https://cloudcdn-fra-02.cdn.yandex.net/cdnrphoszsa2sp7ilm7a';

    return [
        'mid' => 'abc',
        'latency' => ['probes' => [
            ['url' => $base.'/ping?mid=abc&lid=299'],
        ]],
        'download' => ['probes' => [
            ['url' => $base.'/probes/50mb?lid=299&mid=abc'],
            ['url' => $base.'/probes/100kb?lid=299&mid=abc&timeout=100', 'timeout' => 100],
            ['url' => $base.'/probes/50mb?lid=300&mid=abc'],
        ]],
        'upload' => ['probes' => [
            ['size' => 30720, 'postUrl' => $base.'/upload-http?type=upload&sid=one&mid=abc'],
            ['size' => 10240, 'postUrl' => $base.'/upload-http?type=upload&sid=two&mid=abc', 'timeout' => 100],
            ['size' => 30720, 'postUrl' => $base.'/upload-http?type=upload&sid=three&mid=abc'],
        ]],
    ];
}

test('the warm-up probes are left out of the plan', function () {
    $plan = YandexSpeedtest::planFromProbes(yandexProbes());

    expect($plan->latencyUrl)->toEndWith('/ping?mid=abc&lid=299')
        ->and($plan->downloadUrls)->toHaveCount(2)
        ->and($plan->downloadUrls)->each->toContain('/probes/50mb')
        ->and($plan->uploadUrls)->toHaveCount(2)
        ->and($plan->uploadUrls[1])->toContain('sid=three');
});

test('the data center is taken from the probe host', function () {
    $plan = YandexSpeedtest::planFromProbes(yandexProbes());

    expect($plan->server)->toBe([
        'name' => 'Yandex',
        'location' => 'FRA',
        'host' => 'cloudcdn-fra-02.cdn.yandex.net',
    ]);
});

test('a response without latency probes is rejected', function () {
    YandexSpeedtest::planFromProbes(['download' => ['probes' => []]]);
})->throws(RuntimeException::class, 'did not return any latency probes');
