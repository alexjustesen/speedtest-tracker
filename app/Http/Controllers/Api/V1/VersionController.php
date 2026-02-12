<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\GitHub\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VersionController extends ApiController
{
    /**
     * GET /api/v1/version
     * Returns the currently installed application version and update information.
     * Restricted to admin users only.
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->tokenCant('admin:read')) {
            return $this->sendResponse(
                data: null,
                message: 'You do not have permission to view version information.',
                code: Response::HTTP_FORBIDDEN
            );
        }

        $latestVersion = Repository::getLatestVersion();

        return $this->sendResponse(
            data: [
                'app' => [
                    'version' => config('speedtest.build_version'),
                    'build_date' => config('speedtest.build_date')->toIso8601String(),
                ],
                'updates' => [
                    'latest_version' => $latestVersion ?: null,
                    'update_available' => Repository::updateAvailable(),
                ],
            ]
        );
    }
}
