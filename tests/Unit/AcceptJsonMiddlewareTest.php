<?php

test('AcceptJsonMiddleware accepts requests without Accept header (Laravel default)', function () {
    // Laravel's acceptsJson() returns true when no Accept header is present
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET');

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(200);

    $content = json_decode($response->getContent(), true);
    expect($content['success'])->toBe(true);
});

test('AcceptJsonMiddleware accepts requests with Accept: application/json header', function () {
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(200);

    $content = json_decode($response->getContent(), true);
    expect($content['success'])->toBe(true);
});

test('AcceptJsonMiddleware rejects requests with Accept: */json header', function () {
    // Laravel's acceptsJson() returns false for */json
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => '*/json',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(406);

    $content = json_decode($response->getContent(), true);
    expect($content['message'])->toBe('This endpoint only accepts JSON. Please include "Accept: application/json" in your request headers.');
    expect($content['error'])->toBe('Unsupported Media Type');
});

test('AcceptJsonMiddleware accepts requests with Accept: application/* header', function () {
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/*',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(200);
});

test('AcceptJsonMiddleware accepts requests with multiple Accept headers including application/json', function () {
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'text/html,application/json,application/xml;q=0.9,*/*;q=0.8',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(200);
});

test('AcceptJsonMiddleware rejects requests with only non-JSON Accept headers', function () {
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'text/html,application/xml',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(406);

    $content = json_decode($response->getContent(), true);
    expect($content['message'])->toBe('This endpoint only accepts JSON. Please include "Accept: application/json" in your request headers.');
    expect($content['error'])->toBe('Unsupported Media Type');
});

test('AcceptJsonMiddleware sets Content-Type header to application/json when not already set', function () {
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = $middleware->handle($request, function () {
        return response(['success' => true]);
    });

    expect($response->headers->get('Content-Type'))->toBe('application/json');
});

test('AcceptJsonMiddleware preserves existing application/json Content-Type header', function () {
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->headers->get('Content-Type'))->toContain('application/json');
});

test('AcceptJsonMiddleware rejects requests that only accept HTML', function () {
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'text/html',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(406);

    $content = json_decode($response->getContent(), true);
    expect($content['message'])->toBe('This endpoint only accepts JSON. Please include "Accept: application/json" in your request headers.');
    expect($content['error'])->toBe('Unsupported Media Type');
});

test('AcceptJsonMiddleware accepts requests with */* Accept header', function () {
    // Laravel's acceptsJson() returns true for */*
    $middleware = new \App\Http\Middleware\AcceptJsonMiddleware;

    $request = \Illuminate\Http\Request::create('/api/test', 'GET', [], [], [], [
        'HTTP_ACCEPT' => '*/*',
    ]);

    $response = $middleware->handle($request, function () {
        return response()->json(['success' => true]);
    });

    expect($response->getStatusCode())->toBe(200);
});
