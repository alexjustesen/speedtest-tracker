<?php

namespace App\Sso;

use App\Sso\Contracts\SsoConnectionTester;
use Illuminate\Http\Client\Factory as HttpClient;

final class TestSsoConnection implements SsoConnectionTester
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    public function test(string $provider, ?string $baseUrl, ?string $clientId, ?string $clientSecret): SsoConnectionResult
    {
        $tokenPath = config("sso.providers.{$provider}.token_path");

        if (blank($provider) || blank($baseUrl) || blank($clientId) || blank($tokenPath)) {
            return new SsoConnectionResult(false, __('settings/sso.test_missing_config'));
        }

        $endpoint = rtrim($baseUrl, '/').$tokenPath;

        try {
            $response = $this->http
                ->asForm()
                ->timeout(10)
                ->post($endpoint, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ]);
        } catch (\Throwable) {
            return new SsoConnectionResult(false, __('settings/sso.test_unreachable'));
        }

        $error = $response->json('error');

        if ($response->successful() || ($response->clientError() && $response->status() !== 401 && $error !== 'invalid_client')) {
            return new SsoConnectionResult(true, __('settings/sso.test_success'));
        }

        return new SsoConnectionResult(false, __('settings/sso.test_failed'));
    }
}
