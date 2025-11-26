<?php

use App\Models\Result;

it('returns server list', function () {
    Result::factory()->count(3)->create([
        'server_id' => 123,
        'server_name' => 'Test Server A',
    ]);

    Result::factory()->count(2)->create([
        'server_id' => 456,
        'server_name' => 'Test Server B',
    ]);

    $response = $this->getJson('/api/public/servers');

    $response->assertOk();
    $response->assertJsonStructure([
        '*' => ['server_id', 'server_name', 'test_count'],
    ]);

    $servers = $response->json();
    expect($servers)->toHaveCount(2);
    expect($servers[0]['test_count'])->toBe(3); // Server A should be first (more tests)
    expect($servers[1]['test_count'])->toBe(2); // Server B should be second
});
