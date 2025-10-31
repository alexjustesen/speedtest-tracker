<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class UserResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user-reset-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the password for a user.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = text(
            label: __('translations.user_change.welcome_email'),
            required: true,
            validate: fn (string $value) => match (true) {
                ! User::firstWhere('email', $value) => 'User not found.',
                default => null
            }
        );

        $password = password(
            label: __('translations.user_change.what_is_password'),
            required: true,
        );

        User::where('email', '=', $email)
            ->update([
                'password' => Hash::make($password),
            ]);

        info(__('translations.user_change.password_updated_info', ['email' => $email]));
    }
}
