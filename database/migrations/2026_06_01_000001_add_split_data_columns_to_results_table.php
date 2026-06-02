<?php

use App\Data\OoklaResult;
use App\Models\Result;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add dedicated columns for each Ookla metric so they can be queried and
     * indexed individually. Existing rows are backfilled from the `data` JSON
     * blob, which is kept in place for now and will be dropped in a follow-up.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->float('ping_jitter', 8, 3)->nullable()->after('ping');
            $table->float('ping_low', 8, 3)->nullable()->after('ping_jitter');
            $table->float('ping_high', 8, 3)->nullable()->after('ping_low');
            $table->float('download_jitter', 8, 3)->nullable()->after('download');
            $table->float('download_latency_iqm', 8, 3)->nullable()->after('download_jitter');
            $table->float('download_latency_low', 8, 3)->nullable()->after('download_latency_iqm');
            $table->float('download_latency_high', 8, 3)->nullable()->after('download_latency_low');
            $table->unsignedInteger('download_elapsed')->nullable()->after('download_latency_high');
            $table->float('upload_jitter', 8, 3)->nullable()->after('upload');
            $table->float('upload_latency_iqm', 8, 3)->nullable()->after('upload_jitter');
            $table->float('upload_latency_low', 8, 3)->nullable()->after('upload_latency_iqm');
            $table->float('upload_latency_high', 8, 3)->nullable()->after('upload_latency_low');
            $table->unsignedInteger('upload_elapsed')->nullable()->after('upload_latency_high');
            $table->float('packet_loss', 8, 3)->nullable()->after('upload_elapsed');
            $table->string('isp')->nullable()->after('packet_loss');
            $table->string('ip_address')->nullable()->after('isp');
            $table->unsignedInteger('server_id')->nullable()->after('ip_address');
            $table->string('server_name')->nullable()->after('server_id');
            $table->string('server_host')->nullable()->after('server_name');
            $table->string('server_ip')->nullable()->after('server_host');
            $table->string('server_country')->nullable()->after('server_ip');
            $table->string('server_location')->nullable()->after('server_country');
            $table->string('result_url')->nullable()->after('server_location');
            $table->text('error_message')->nullable()->after('status');
        });

        // Backfill the new columns from the existing JSON blob.
        Result::whereNotNull('data')->lazy()->each(function (Result $result) {
            $result->updateQuietly([
                ...OoklaResult::fromArray($result->data)->toModelAttributes(),
                'error_message' => Arr::get($result->data, 'message'),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn([
                'ping_jitter', 'ping_low', 'ping_high',
                'download_jitter', 'download_latency_iqm', 'download_latency_low', 'download_latency_high', 'download_elapsed',
                'upload_jitter', 'upload_latency_iqm', 'upload_latency_low', 'upload_latency_high', 'upload_elapsed',
                'packet_loss', 'isp', 'ip_address',
                'server_id', 'server_name', 'server_host', 'server_ip', 'server_country', 'server_location',
                'result_url', 'error_message',
            ]);
        });
    }
};
