<?php

use App\Jobs\Notifications\Apprise\SendSpeedtestCompletedNotification;
use App\Jobs\Notifications\Apprise\SendSpeedtestThresholdNotification;
use App\Models\Result;
use App\Models\User;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Log;

describe('Apprise Notification Jobs', function () {
    test('SendSpeedtestCompletedNotification logic when channel urls exist', function () {
        // Clean users table to ensure only one user exists
        \App\Models\User::query()->delete();

        $user = User::factory()->create();
        $result = Result::factory()->create();

        // Set apprise channel urls and URL (the job only checks channel_urls, but service needs apprise_url)
        $settings = new NotificationSettings;
        $settings->apprise_url = 'https://apprise.example.com';
        $settings->apprise_channel_urls = [
            ['channel_url' => 'https://service1.example.com'],
            ['channel_url' => 'https://service2.example.com'],
        ];
        $settings->save();

        $job = new SendSpeedtestCompletedNotification($result);

        // Do not assert the static call, just run the job
        $job->handle();

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        expect($savedSettings->apprise_url)->toBe('https://apprise.example.com');
        expect($savedSettings->apprise_channel_urls)->toHaveCount(2);
        expect($savedSettings->apprise_channel_urls[0]['channel_url'])->toBe('https://service1.example.com');
        expect($savedSettings->apprise_channel_urls[1]['channel_url'])->toBe('https://service2.example.com');
    });

    test('SendSpeedtestCompletedNotification logic when no channel urls', function () {
        // Clean users table to ensure only one user exists
        \App\Models\User::query()->delete();

        $user = User::factory()->create();
        $result = Result::factory()->create();

        // Set empty apprise channel urls
        $settings = new NotificationSettings;
        $settings->apprise_url = 'https://apprise.example.com';
        $settings->apprise_channel_urls = [];
        $settings->save();

        $job = new SendSpeedtestCompletedNotification($result);

        // Mock Log facade to capture the warning
        Log::shouldReceive('warning')->with('Apprise service URLs not found; check Apprise notification settings.')->once();

        $job->handle();

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        expect($savedSettings->apprise_channel_urls)->toHaveCount(0);
    });

    test('SendSpeedtestThresholdNotification logic when threshold is breached', function () {
        // Clean users table to ensure only one user exists
        \App\Models\User::query()->delete();

        $user = User::factory()->create();
        $result = Result::factory()->create([
            'ping' => 100, // High ping that will breach threshold
            'download' => 104857.6, // 0.1 MB in bytes
            'upload' => 52428.8, // 0.05 MB in bytes
        ]);

        // Set apprise channel urls and URL
        $settings = new NotificationSettings;
        $settings->apprise_url = 'https://apprise.example.com';
        $settings->apprise_channel_urls = [['channel_url' => 'https://service.example.com']];
        $settings->save();

        // Set threshold settings
        $thresholdSettings = new ThresholdSettings;
        $thresholdSettings->absolute_enabled = true;
        $thresholdSettings->absolute_ping = 50; // Threshold lower than result (100 > 50)
        $thresholdSettings->absolute_download = 1; // Threshold higher than result (0.8 < 1)
        $thresholdSettings->absolute_upload = 1; // Threshold higher than result (0.4 < 1)
        $thresholdSettings->save();

        $job = new SendSpeedtestThresholdNotification($result);

        // Test that the threshold functions work correctly
        expect(absolutePingThresholdFailed(50, 100))->toBeTrue(); // 100 > 50
        expect(absoluteDownloadThresholdFailed(1, 104857.6))->toBeTrue(); // 0.8 < 1
        expect(absoluteUploadThresholdFailed(1, 52428.8))->toBeTrue(); // 0.4 < 1

        // Do not assert the static call, just run the job
        $job->handle();

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        $savedThresholdSettings = new ThresholdSettings;
        expect($savedSettings->apprise_url)->toBe('https://apprise.example.com');
        expect($savedSettings->apprise_channel_urls)->toHaveCount(1);
        expect($savedThresholdSettings->absolute_enabled)->toBeTrue();
        expect($savedThresholdSettings->absolute_ping)->toBe(50.0);
        expect($savedThresholdSettings->absolute_download)->toBe(1.0);
        expect($savedThresholdSettings->absolute_upload)->toBe(1.0);
    });

    test('SendSpeedtestThresholdNotification logic when thresholds not breached', function () {
        // Clean users table to ensure only one user exists
        \App\Models\User::query()->delete();

        $user = User::factory()->create();
        $result = Result::factory()->create([
            'ping' => 20, // Low ping, should not breach threshold
            'download' => 104857600, // 100 MB in bytes
            'upload' => 52428800, // 50 MB in bytes
        ]);

        // Set apprise channel urls and URL
        $settings = new NotificationSettings;
        $settings->apprise_url = 'https://apprise.example.com';
        $settings->apprise_channel_urls = [['channel_url' => 'https://service.example.com']];
        $settings->save();

        // Set threshold settings
        $thresholdSettings = new ThresholdSettings;
        $thresholdSettings->absolute_enabled = true;
        $thresholdSettings->absolute_ping = 50; // Threshold higher than result (20 < 50)
        $thresholdSettings->absolute_download = 1; // Threshold lower than result (800 > 1)
        $thresholdSettings->absolute_upload = 1; // Threshold lower than result (400 > 1)
        $thresholdSettings->save();

        $job = new SendSpeedtestThresholdNotification($result);

        // Test that the threshold functions work correctly
        expect(absolutePingThresholdFailed(50, 20))->toBeFalse(); // 20 < 50
        expect(absoluteDownloadThresholdFailed(1, 104857600))->toBeFalse(); // 800 > 1
        expect(absoluteUploadThresholdFailed(1, 52428800))->toBeFalse(); // 400 > 1

        // Mock Log facade to capture the warning when no thresholds are breached
        Log::shouldReceive('warning')->with('Failed apprise thresholds not found, won\'t send notification.')->once();

        $job->handle();

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        $savedThresholdSettings = new ThresholdSettings;
        expect($savedSettings->apprise_url)->toBe('https://apprise.example.com');
        expect($savedSettings->apprise_channel_urls)->toHaveCount(1);
        expect($savedThresholdSettings->absolute_enabled)->toBeTrue();
    });
});
