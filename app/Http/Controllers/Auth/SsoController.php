<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Sso\SsoCallbackRequest;
use App\Http\Requests\Sso\SsoRedirectRequest;
use App\Providers\AppServiceProvider;
use App\Sso\Contracts\SsoUserResolver;
use App\Sso\SsoManager;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\RedirectResponse;
use Psr\Log\LoggerInterface;

final readonly class SsoController
{
    public function __construct(
        private readonly SsoManager $manager,
        private readonly SsoUserResolver $resolver,
        private readonly Auth $auth,
        private readonly LoggerInterface $logger,
    ) {}

    public function redirect(SsoRedirectRequest $request): RedirectResponse
    {
        return $this->manager->driver((string) $request->route('provider'))->redirect();
    }

    public function callback(SsoCallbackRequest $request): RedirectResponse
    {
        $provider = (string) $request->route('provider');

        try {
            $user = $this->resolver->resolve($provider, $this->manager->driver($provider)->user());
        } catch (\Throwable $exception) {
            $this->logger->warning('SSO authentication failed.', [
                'provider' => $provider,
                'message' => $exception->getMessage(),
            ]);

            return $this->failed(__('settings/sso.callback_failed'));
        }

        if ($user === null) {
            return $this->failed(__('settings/sso.not_provisioned'));
        }

        $this->auth->guard('web')->login($user, remember: true);

        $request->session()->regenerate();

        return redirect()->intended(AppServiceProvider::HOME);
    }

    private function failed(string $message): RedirectResponse
    {
        Notification::make()
            ->title($message)
            ->danger()
            ->send();

        return redirect()->route('filament.admin.auth.login');
    }
}
