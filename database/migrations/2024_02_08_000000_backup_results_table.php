<?php

use App\Models\User;
use App\Settings\GeneralSettings;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('results')) {
            /**
             * Rename the existing table so that a backup copy exists.
             */
            Schema::rename('results', 'results_bak_bad_json');
        }

        if (! Schema::hasTable('results')) {
            /**
             * Create a new results table based on a new DDL schema.
             */
            Schema::create('results', function (Blueprint $table) {
                $table->id();
                $table->string('service')->default('ookla');
                $table->float('ping', 8, 3)->nullable();
                $table->unsignedBigInteger('download')->nullable();
                $table->unsignedBigInteger('upload')->nullable();
                $table->text('comments')->nullable();
                $table->json('data')->nullable();
                $table->string('status')->default('pending');
                $table->boolean('scheduled')->default(false);
                $table->timestamps();
            });
        }

        /**
         * Don't disable the schedule or send a notification if there are no records.
         */
        if (! DB::table('results_bak_bad_json')->count()) {
            return;
        }

        $settings = new GeneralSettings();

        $settings->speedtest_schedule = '';

        $settings->save();

        $admins = User::select(['id', 'name', 'email', 'role'])
            ->where('role', 'admin')
            ->get();

        foreach ($admins as $user) {
            Notification::make()
                ->title('Breaking change, action required!')
                ->body('v0.16.0 includes a breaking change to resolve a data quality issue. Read the docs below to migrate your data.')
                ->danger()
                ->actions([
                    Action::make('docs')
                        ->button()
                        ->url('https://docs.speedtest-tracker.dev/')
                        ->openUrlInNewTab(),
                ])
                ->sendToDatabase($user);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('results');

        if (! Schema::hasTable('results')) {
            Schema::rename('results_bak_bad_json', 'results');
        }
    }
};
