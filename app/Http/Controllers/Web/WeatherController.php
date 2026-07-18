<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * WeatherController (Lab 12 — Task 1 & 3)
 *
 * Fetches live weather data from Open-Meteo (https://open-meteo.com/)
 * — 100% free, open-source, no API key required.
 *
 * Features:
 *  - getWeather()     : Used by the dashboard widget (server-rendered Blade)
 *  - getWeatherJson() : JSON endpoint consumed by the AJAX weather search
 *
 * Open-Meteo API endpoint:
 *   GET https://api.open-meteo.com/v1/forecast
 *       ?latitude={lat}&longitude={lon}&current_weather=true
 *
 * Geolocation via ip-api.com (already used elsewhere in the project).
 */
class WeatherController extends Controller
{
    /** WMO weather interpretation codes → human-readable description */
    private const WMO_CODES = [
        0  => 'Clear sky',
        1  => 'Mainly clear',
        2  => 'Partly cloudy',
        3  => 'Overcast',
        45 => 'Foggy',
        48 => 'Icy fog',
        51 => 'Light drizzle',
        53 => 'Moderate drizzle',
        55 => 'Dense drizzle',
        61 => 'Slight rain',
        63 => 'Moderate rain',
        65 => 'Heavy rain',
        71 => 'Slight snow',
        73 => 'Moderate snow',
        75 => 'Heavy snow',
        80 => 'Slight showers',
        81 => 'Moderate showers',
        82 => 'Violent showers',
        95 => 'Thunderstorm',
        99 => 'Thunderstorm with hail',
    ];

    // ── Dashboard Widget (server-rendered) ────────────────────────────────────

    /**
     * Fetch current weather for the user's detected location.
     * Returns null on any failure so the dashboard can render without it.
     */
    public function getWeather(): ?array
    {
        try {
            $location = $this->getLocation();
            if (!$location) return null;

            $client = new Client(['timeout' => 5]);

            $response = $client->get('https://api.open-meteo.com/v1/forecast', [
                'query' => [
                    'latitude'        => $location['lat'],
                    'longitude'       => $location['lon'],
                    'current_weather' => 'true',
                    'wind_speed_unit' => 'kmh',
                ],
            ]);

            $data    = json_decode($response->getBody()->getContents(), true);
            $current = $data['current_weather'] ?? null;
            if (!$current) return null;

            $code = (int) ($current['weathercode'] ?? 0);

            return [
                'city'        => $location['city'],
                'country'     => $location['country'],
                'temperature' => round((float) ($current['temperature'] ?? 0), 1),
                'windspeed'   => round((float) ($current['windspeed']   ?? 0), 1),
                'description' => self::WMO_CODES[$code] ?? 'Unknown',
                'is_day'      => (bool) ($current['is_day'] ?? true),
                'code'        => $code,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    // ── AJAX JSON Endpoint (Lab 12 Task 3) ────────────────────────────────────

    /**
     * Return weather JSON for a given city name (AJAX search).
     *
     * GET /weather-json?city=Dhaka
     *
     * Response: { city, country, temperature, windspeed, description, is_day }
     */
    public function getWeatherJson(Request $request): JsonResponse
    {
        $city = trim((string) $request->query('city', ''));

        if ($city === '') {
            return response()->json(['error' => 'Please enter a city name.'], 422);
        }

        try {
            $client = new Client(['timeout' => 5]);

            // Step 1 — Geocode the city using Open-Meteo's geocoding API (free, no key)
            $geoResponse = $client->get('https://geocoding-api.open-meteo.com/v1/search', [
                'query' => ['name' => $city, 'count' => 1, 'language' => 'en', 'format' => 'json'],
            ]);
            $geoData = json_decode($geoResponse->getBody()->getContents(), true);

            $results = $geoData['results'] ?? [];
            if (empty($results)) {
                return response()->json(['error' => "City \"{$city}\" not found."], 404);
            }

            $place = $results[0];
            $lat   = $place['latitude'];
            $lon   = $place['longitude'];
            $name  = $place['name'];
            $country = $place['country'] ?? '';

            // Step 2 — Fetch weather for those coordinates
            $weatherResponse = $client->get('https://api.open-meteo.com/v1/forecast', [
                'query' => [
                    'latitude'        => $lat,
                    'longitude'       => $lon,
                    'current_weather' => 'true',
                    'wind_speed_unit' => 'kmh',
                ],
            ]);
            $weatherData = json_decode($weatherResponse->getBody()->getContents(), true);
            $current     = $weatherData['current_weather'] ?? null;

            if (!$current) {
                return response()->json(['error' => 'Weather data unavailable.'], 503);
            }

            $code = (int) ($current['weathercode'] ?? 0);

            return response()->json([
                'city'        => $name,
                'country'     => $country,
                'temperature' => round((float) ($current['temperature'] ?? 0), 1),
                'windspeed'   => round((float) ($current['windspeed']   ?? 0), 1),
                'description' => self::WMO_CODES[$code] ?? 'Unknown',
                'is_day'      => (bool) ($current['is_day'] ?? true),
            ]);
        } catch (GuzzleException $e) {
            return response()->json(['error' => 'Could not reach weather service. Try again.'], 503);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unexpected error.'], 500);
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    private function getLocation(): ?array
    {
        try {
            $client   = new Client(['timeout' => 5]);
            $response = $client->get('http://ip-api.com/json');
            $data     = json_decode($response->getBody()->getContents(), true);

            if (($data['status'] ?? '') !== 'success') return null;

            return [
                'city'    => $data['city']        ?? 'Unknown',
                'country' => $data['country']     ?? 'Unknown',
                'lat'     => (float) ($data['lat'] ?? 0),
                'lon'     => (float) ($data['lon'] ?? 0),
            ];
        } catch (\Throwable) {
            return null;
        }
    }
}
