<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-user-password
                            {email : The email address of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the password for a user\'s account.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::firstWhere('email', $this->argument('email'));

        if (! $user) {
            // couldn't find the user so should fail.
            $this->error('Could not find a user with the email address of '.$$this->argument('email'));

            Command::FAILURE;
        }

        $password = $this->secret('What is the password?');

        $user->update([
            'password' => Hash::make($password),
        ]);

        Command::SUCCESS;
    }
}
