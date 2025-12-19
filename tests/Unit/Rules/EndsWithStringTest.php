<?php

use App\Rules\EndsWithString;

it('passes when string ends with suffix', function () {
    $rule = new EndsWithString('/notify');
    $fails = false;

    $rule->validate('url', 'http://localhost:8000/notify', function () use (&$fails) {
        $fails = true;
    });

    expect($fails)->toBeFalse();
});

it('fails when string does not end with suffix', function () {
    $rule = new EndsWithString('/notify');
    $fails = false;

    $rule->validate('url', 'http://localhost:8000', function () use (&$fails) {
        $fails = true;
    });

    expect($fails)->toBeTrue();
});

it('is case insensitive by default', function () {
    $rule = new EndsWithString('/notify');
    $fails = false;

    $rule->validate('url', 'http://localhost:8000/NOTIFY', function () use (&$fails) {
        $fails = true;
    });

    expect($fails)->toBeFalse();
});

it('can be case sensitive', function () {
    $rule = new EndsWithString('/notify', caseSensitive: true);
    $fails = false;

    $rule->validate('url', 'http://localhost:8000/NOTIFY', function () use (&$fails) {
        $fails = true;
    });

    expect($fails)->toBeTrue();
});

it('passes when string ends with suffix with trailing slash', function () {
    $rule = new EndsWithString('/notify');
    $fails = false;

    $rule->validate('url', 'http://localhost:8000/notify/', function () use (&$fails) {
        $fails = true;
    });

    expect($fails)->toBeTrue();
});
