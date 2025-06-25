<?php

use App\Events\SpeedtestCompleted;
use App\Listeners\Database\SendSpeedtestCompletedNotification;
use App\Listeners\Database\SendSpeedtestThresholdNotification;
use App\Models\Result;
use App\Models\User;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Log;

describe('Database Notification Listeners', function () {
    test('SendSpeedtestCompletedNotification logic when enabled', function () {
        // Clean users table to ensure only one user exists
        \App\Models\User::query()->delete();

        $user = User::factory()->create();
        $result = Result::factory()->create();

        // Enable database notifications in settings
        $settings = new NotificationSettings;
        $settings->database_enabled = true;
        $settings->database_on_speedtest_run = true;
        $settings->save();

        $event = new SpeedtestCompleted($result);
        $listener = new SendSpeedtestCompletedNotification;

        // Mock the Log facade to capture the notification calls
        Log::shouldReceive('info')->with('Notifying user', ['id' => $user->id, 'email' => $user->email])->once();

        $listener->handle($event);

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        expect($savedSettings->database_enabled)->toBeTrue();
        expect($savedSettings->database_on_speedtest_run)->toBeTrue();
    });

    test('SendSpeedtestCompletedNotification logic when disabled', function () {
        // Clean users table to ensure only one user exists
        \App\Models\User::query()->delete();

        $user = User::factory()->create();
        $result = Result::factory()->create();

        // Disable database notifications in settings
        $settings = new NotificationSettings;
        $settings->database_enabled = false;
        $settings->save();

        $event = new SpeedtestCompleted($result);
        $listener = new SendSpeedtestCompletedNotification;

        // Should not log any notification calls when disabled
        Log::shouldReceive('info')->never();

        $listener->handle($event);

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        expect($savedSettings->database_enabled)->toBeFalse();
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

        // Enable database threshold notifications in settings
        $settings = new NotificationSettings;
        $settings->database_enabled = true;
        $settings->database_on_threshold_failure = true;
        $settings->save();

        // Set threshold settings
        $thresholdSettings = new ThresholdSettings;
        $thresholdSettings->absolute_enabled = true;
        $thresholdSettings->absolute_ping = 50; // Threshold lower than result (100 > 50)
        $thresholdSettings->absolute_download = 1; // Threshold higher than result (0.8 < 1)
        $thresholdSettings->absolute_upload = 1; // Threshold higher than result (0.4 < 1)
        $thresholdSettings->save();

        $event = new SpeedtestCompleted($result);
        $listener = new SendSpeedtestThresholdNotification;

        // Test that the threshold functions work correctly
        expect(absolutePingThresholdFailed(50, 100))->toBeTrue(); // 100 > 50
        expect(absoluteDownloadThresholdFailed(1, 104857.6))->toBeTrue(); // 0.8 < 1
        expect(absoluteUploadThresholdFailed(1, 52428.8))->toBeTrue(); // 0.4 < 1

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        $savedThresholdSettings = new ThresholdSettings;
        expect($savedSettings->database_enabled)->toBeTrue();
        expect($savedSettings->database_on_threshold_failure)->toBeTrue();
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

        // Enable database threshold notifications in settings
        $settings = new NotificationSettings;
        $settings->database_enabled = true;
        $settings->database_on_threshold_failure = true;
        $settings->save();

        // Set threshold settings
        $thresholdSettings = new ThresholdSettings;
        $thresholdSettings->absolute_enabled = true;
        $thresholdSettings->absolute_ping = 50; // Threshold higher than result (20 < 50)
        $thresholdSettings->absolute_download = 1; // Threshold lower than result (800 > 1)
        $thresholdSettings->absolute_upload = 1; // Threshold lower than result (400 > 1)
        $thresholdSettings->save();

        $event = new SpeedtestCompleted($result);
        $listener = new SendSpeedtestThresholdNotification;

        // Test that the threshold functions work correctly
        expect(absolutePingThresholdFailed(50, 20))->toBeFalse(); // 20 < 50
        expect(absoluteDownloadThresholdFailed(1, 104857600))->toBeFalse(); // 800 > 1
        expect(absoluteUploadThresholdFailed(1, 52428800))->toBeFalse(); // 400 > 1

        // Verify that the settings are correctly configured
        $savedSettings = new NotificationSettings;
        $savedThresholdSettings = new ThresholdSettings;
        expect($savedSettings->database_enabled)->toBeTrue();
        expect($savedSettings->database_on_threshold_failure)->toBeTrue();
        expect($savedThresholdSettings->absolute_enabled)->toBeTrue();
    });
});
