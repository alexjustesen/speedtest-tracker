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
            label: 'What is the email address?',
            required: true,
            validate: fn (string $value) => match (true) {
                ! User::firstWhere('email', $value) => 'User not found.',
                default => null
            }
        );

        $role = select(
            label: 'What role should the user have?',
            options: [
                'admin' => 'Admin',
                'user' => 'User',
            ],
            default: 'user'
        );

        User::where('email', '=', $email)
            ->update([
                'role' => $role,
            ]);

        info('User role updated.');
    }
}
