<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmailService
{
    /**
     * Get client information from request.
     */
    public static function getClientInfo(Request $request): array
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        
        // Parse browser from user agent
        $browser = self::parseBrowser($userAgent);
        
        // Get location from IP (using ipinfo.io free tier)
        $location = self::getLocationFromIp($ip);
        
        return [
            'ip_address' => $ip,
            'browser' => $browser,
            'location' => $location,
            'user_agent' => $userAgent,
        ];
    }

    /**
     * Parse browser name from user agent.
     */
    protected static function parseBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Edg') === false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edg') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            return 'Opera';
        } elseif (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile Browser';
        }

        return 'Unknown Browser';
    }

    /**
     * Get location from IP address.
     */
    protected static function getLocationFromIp(string $ip): string
    {
        // Skip local/private IPs
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return 'Local Network';
        }

        try {
            // Using ipinfo.io free API (1000 requests/day free)
            $response = Http::timeout(3)->get("https://ipinfo.io/{$ip}/json");
            
            if ($response->successful()) {
                $data = $response->json();
                $city = $data['city'] ?? '';
                $region = $data['region'] ?? '';
                $country = $data['country'] ?? '';
                
                $parts = array_filter([$city, $region, $country]);
                return !empty($parts) ? implode(', ', $parts) : 'Unknown Location';
            }
        } catch (\Exception $e) {
            // Fallback if API fails
        }

        return 'Unknown Location';
    }
}

