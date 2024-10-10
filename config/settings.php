<?php

return [

    /*
     * Each settings class used in your application must be registered here.
     */
    'settings' => [
        \App\Settings\LatencySettings::class,  // Register the settings class here as a string
    ],

    /*
     * The path where the settings classes will be created.
     */
    'setting_class_path' => app_path('Settings'),

    /*
     * In these directories, settings migrations will be stored and run when migrating. 
     */
    'migrations_paths' => [
        database_path('settings'),
    ],

    /*
     * When no repository is set for a settings class, the following repository will be used.
     */
    'default_repository' => 'database',

    /*
     * Settings will be stored and loaded from these repositories.
     */
    'repositories' => [
        'database' => [
            'type' => Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository::class,
            'model' => null,
            'table' => null,
            'connection' => null,
        ],
        'redis' => [
            'type' => Spatie\LaravelSettings\SettingsRepositories\RedisSettingsRepository::class,
            'connection' => null,
            'prefix' => null,
        ],
    ],

    /*
     * Settings caching configuration.
     */
    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', false),
        'store' => env('CACHE_STORE', 'database'),
        'prefix' => null,
        'ttl' => null,
    ],

    /*
     * Global casts for non-PHP type properties in settings classes.
     */
    'global_casts' => [
        DateTimeInterface::class => Spatie\LaravelSettings\SettingsCasts\DateTimeInterfaceCast::class,
        DateTimeZone::class => Spatie\LaravelSettings\SettingsCasts\DateTimeZoneCast::class,
    ],

    /*
     * Paths for automatically discovering settings classes.
     */
    'auto_discover_settings' => [
        app_path('Settings'),
    ],

    /*
     * Path to cache discovered settings classes.
     */
    'discovered_settings_cache_path' => base_path('bootstrap/cache'),
];
