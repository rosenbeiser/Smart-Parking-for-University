<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

/**
 * CountryMiddleware (Lab 12 — Task 4)
 *
 * Validates that the incoming request originates from an allowed country
 * by querying ip-api.com with the client's IP address using Guzzle.
 *
 * Usage in routes:  ->middleware('country:BD')
 * Default country:  BD (Bangladesh)
 */
class CountryMiddleware
{
    public function handle(Request $request, Closure $next, string $country = 'BD'): mixed
    {
        // In local/testing environments, skip the country check
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        $clientIp = $request->ip();

        try {
            $client   = new Client(['timeout' => 5]);
            $response = $client->get("http://ip-api.com/json/{$clientIp}");
            $data     = json_decode($response->getBody()->getContents(), true);

            $detectedCountry = strtoupper($data['countryCode'] ?? '');

            if ($detectedCountry !== strtoupper($country)) {
                return redirect()
                    ->route('student.dashboard')
                    ->with('error', "Access restricted: this page is only available from {$country}. Your location: {$detectedCountry}.");
            }
        } catch (GuzzleException $e) {
            // If the geolocation API is unreachable, allow the request through
            // rather than blocking legitimate users due to a network issue
            return $next($request);
        }

        return $next($request);
    }
}
