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
            label: 'What is the email address?',
            required: true,
            validate: fn (string $value) => match (true) {
                ! User::firstWhere('email', $value) => 'User not found.',
                default => null
            }
        );

        $password = password(
            label: 'What is the new password?',
            required: true,
        );

        User::where('email', '=', $email)
            ->update([
                'password' => Hash::make($password),
            ]);

        info('The password for "'.$email.'" has been updated.');
    }
}
