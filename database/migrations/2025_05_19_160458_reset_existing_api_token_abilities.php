<?php

use Illuminate\Database\Migrations\Migration;
use Laravel\Sanctum\PersonalAccessToken;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        PersonalAccessToken::all()->each(function (PersonalAccessToken $token) {
            $token->abilities = [
                'results:read',
            ];

            $token->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
