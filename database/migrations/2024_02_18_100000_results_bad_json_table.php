<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Settings\DataMigrationSettings;
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
        if (! Schema::hasTable('results_bad_json')) {
            if (Schema::hasTable('results')) {
                /**
                 * Rename the existing table so that a backup copy exists.
                 */
                Schema::rename('results', 'results_bad_json');
            }

            if (! Schema::hasTable('results') && Schema::hasTable('results_bad_json')) {
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
                    $table->string('status');
                    $table->boolean('scheduled')->default(false);
                    $table->timestamps();
                });
            }
        }

        /**
         * Don't disable the schedule or send a notification if there are no records.
         */
        if (! DB::table('results_bad_json')->count()) {
            $dataSettings = new DataMigrationSettings;

            $dataSettings->bad_json_migrated = true;

            $dataSettings->save();

            return;
        }

        $admins = User::where('role', '=', UserRole::Admin)->get();

        foreach ($admins as $user) {
            Notification::make()
                ->title('Breaking change, user action required!')
                ->body('v0.16.0 includes a breaking change to resolve a data quality issue. Read the release notes regarding the data migration.')
                ->danger()
                ->actions([
                    Action::make('Release notes')
                        ->button()
                        ->url('https://github.com/alexjustesen/speedtest-tracker/releases/tag/v0.16.0')
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
            Schema::rename('results_bad_json', 'results');
        }
    }
};
