<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PublicIpService
{
    /**
     * Get the public IP address and its associated details using ipapi.co.
     */
    public function getPublicIp(): array
    {
        try {
            // Fetch location data from ifconfig.co using curl
            $ch = curl_init('https://ifconfig.co/json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Validate the HTTP response
            if ($httpCode !== 200) {
                \Log::error("Failed to fetch public IP data from ifconfig.co. HTTP Status Code: $httpCode");

                return ['ip' => 'unknown', 'isp' => 'unknown'];
            }

            // Decode the JSON response
            $data = json_decode($response, true);

            // Validate the response format
            if (json_last_error() === JSON_ERROR_NONE && isset($data['ip'])) {
                return [
                    'ip' => $data['ip'],
                    'isp' => $data['asn_org'] ?? 'unknown',
                ];
            }

            // Log error if the response is invalid
            \Log::error('Invalid response from ifconfig.co: '.$response);

            return ['ip' => 'unknown', 'isp' => 'unknown'];
        } catch (\Exception $e) {
            \Log::error("Error fetching public IP data from ifconfig.co: {$e->getMessage()}");

            // Fallback response
            return ['ip' => 'unknown', 'isp' => 'unknown'];
        }
    }

    /**
     * Check if the current IP should be skipped.
     */
    public function shouldSkipIp(string $currentIp, array $skipSettings): bool|string
    {
        foreach ($skipSettings as $setting) {
            // Check for exact IP match
            if (filter_var($setting, FILTER_VALIDATE_IP)) {
                if ($currentIp === $setting) {
                    return "Current public IP address ($currentIp) is listed to be skipped for testing.";
                }
            }

            // Check for subnet match
            if (strpos($setting, '/') !== false && $this->ipInSubnet($currentIp, $setting)) {
                return "Current public IP address ($currentIp) falls within the excluded subnet ($setting).";
            }
        }

        return false;
    }

    /**
     * Check if an IP is in a given subnet.
     */
    protected function ipInSubnet(string $ip, string $subnet): bool
    {
        [$subnet, $mask] = explode('/', $subnet) + [1 => '32'];
        $subnetDecimal = ip2long($subnet);
        $ipDecimal = ip2long($ip);
        $maskDecimal = ~((1 << (32 - (int) $mask)) - 1);

        return ($subnetDecimal & $maskDecimal) === ($ipDecimal & $maskDecimal);
    }
}
