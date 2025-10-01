<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class UserChangeRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user-change-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the role for a user.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = text(
            label: __('translations.user_change.what_is_the_email_address'),
            required: true,
            validate: fn (string $value) => match (true) {
                ! User::firstWhere('email', $value) => 'User not found.',
                default => null
            }
        );

        $role = select(
            label: __('translations.user_change.what_role'),
            options: [
                'admin' => __('translations.Admin'),
                'user' => __('translations.User'),
            ],
            default: 'user'
        );

        User::where('email', '=', $email)
            ->update([
                'role' => $role,
            ]);

        info(__('translations.user_change.info'));
    }
}
