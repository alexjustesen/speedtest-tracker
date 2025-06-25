<?php

use App\Settings\NotificationSettings;

describe('Notification Settings', function () {
    test('can create notification settings with default values', function () {
        $settings = new NotificationSettings;

        expect($settings)->toBeInstanceOf(NotificationSettings::class);
        expect($settings->database_enabled)->toBeFalse();
        expect($settings->mail_enabled)->toBeFalse();
        expect($settings->webhook_enabled)->toBeFalse();
        expect($settings->apprise_enabled)->toBeFalse();
    });

    test('returns correct group name', function () {
        expect(NotificationSettings::group())->toBe('notification');
    });

    test('can set and retrieve notification settings for supported channels', function () {
        $settings = new NotificationSettings;

        // Test database settings
        $settings->database_enabled = true;
        $settings->database_on_speedtest_run = true;
        $settings->database_on_threshold_failure = false;

        expect($settings->database_enabled)->toBeTrue();
        expect($settings->database_on_speedtest_run)->toBeTrue();
        expect($settings->database_on_threshold_failure)->toBeFalse();

        // Test mail settings
        $settings->mail_enabled = true;
        $settings->mail_recipients = ['test@example.com'];

        expect($settings->mail_enabled)->toBeTrue();
        expect($settings->mail_recipients)->toBe(['test@example.com']);

        // Test webhook settings
        $settings->webhook_enabled = true;
        $settings->webhook_urls = ['https://webhook.example.com'];

        expect($settings->webhook_enabled)->toBeTrue();
        expect($settings->webhook_urls)->toBe(['https://webhook.example.com']);

        // Test apprise settings
        $settings->apprise_enabled = true;
        $settings->apprise_url = 'https://apprise.example.com';
        $settings->apprise_verify_ssl = true;

        expect($settings->apprise_enabled)->toBeTrue();
        expect($settings->apprise_url)->toBe('https://apprise.example.com');
        expect($settings->apprise_verify_ssl)->toBeTrue();
    });

    test('handles null values correctly', function () {
        $settings = new NotificationSettings;

        $settings->mail_recipients = null;
        $settings->webhook_urls = null;
        $settings->apprise_url = null;

        expect($settings->mail_recipients)->toBeNull();
        expect($settings->webhook_urls)->toBeNull();
        expect($settings->apprise_url)->toBeNull();
    });
});
