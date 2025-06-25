<?php

describe('API - Healthcheck', function () {
    test('returns health status', function () {
        $response = $this->get('/api/healthcheck');

        $response->assertStatus(200);
        // The exact response structure depends on the AppController implementation
        // but it should return some form of health status
    });

    test('does not require authentication', function () {
        $response = $this->get('/api/healthcheck');

        $response->assertStatus(200);
    });

    test('works with different HTTP methods', function () {
        $response = $this->post('/api/healthcheck');

        // Should still work or return appropriate method not allowed
        expect($response->status())->toBeIn([200, 405]);
    });
});
