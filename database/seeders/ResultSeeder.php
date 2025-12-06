<?php

namespace Database\Seeders;

use App\Models\Result;
use Illuminate\Database\Seeder;

class ResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one result per hour for the last 30 days
        $startDate = now()->subDays(30);
        $endDate = now();

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            Result::factory()->create([
                'created_at' => $currentDate->copy(),
                'updated_at' => $currentDate->copy(),
            ]);

            $currentDate->addHour();
        }
    }
}
