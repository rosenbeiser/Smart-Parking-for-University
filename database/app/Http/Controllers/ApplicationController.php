<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ParkingApplication;

class ApplicationController extends Controller
{
    // ── Public Routes ─────────────────────────────────────────────────────────

    /**
     * Show the parking application form.
     * Fetches the applicant's location via ip-api.com and passes it to the view.
     */
    public function create(): \Illuminate\View\View
    {
        $location = $this->getApplicantLocation();

        return view('applications.create', compact('location'));
    }

    /**
     * Store a new parking application.
     * (Stub — extend with your validation and persistence logic as needed.)
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:20',
            'vehicle_type'   => 'required|in:car,motorcycle,bicycle',
            'semester'       => 'required|string|max:50',
        ]);

        // TODO: Persist the application to the database using the existing
        //       parking_applications table and redirect to the status page.
        //       Example:
        //       $app = ParkingApplication::create([...]);
        //       return redirect()->route('applications.status', $app->id);

        return redirect()->route('student.dashboard')
            ->with('success', 'Application submitted successfully.');
    }

    /**
     * Show the status page for a specific application (server-rendered).
     */
    public function status(int $id): \Illuminate\View\View
    {
        $application = ParkingApplication::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('applications.status', compact('application'));
    }

    // ── AJAX Endpoint ─────────────────────────────────────────────────────────

    /**
     * Return JSON with the live status of an application (for AJAX polling).
     *
     * Response shape:
     *   { "status": string, "updated_at": string (diffForHumans), "semester": string }
     */
    public function statusJson(int $id): JsonResponse
    {
        try {
            $application = ParkingApplication::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            return response()->json([
                'status'     => $application->status,
                'updated_at' => $application->updated_at->diffForHumans(),
                'semester'   => optional($application->semester)->name ?? 'N/A',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Unable to fetch application status.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Call ip-api.com to get the applicant's approximate location.
     *
     * Returns an array with keys: city, country, isp.
     * Falls back to "Unknown" values on any network or parsing failure.
     *
     * @return array{city: string, country: string, isp: string}
     */
    private function getApplicantLocation(): array
    {
        try {
            $client   = new Client(['timeout' => 5]);
            $response = $client->get('http://ip-api.com/json');
            $data     = json_decode($response->getBody()->getContents(), true);

            return [
                'city'    => $data['city']    ?? 'Unknown',
                'country' => $data['country'] ?? 'Unknown',
                'isp'     => $data['isp']     ?? 'Unknown',
            ];
        } catch (GuzzleException $e) {
            // Network failure or API unavailable
            return ['city' => 'Unknown', 'country' => 'Unknown', 'isp' => 'Unknown'];
        } catch (\Throwable $e) {
            // JSON parse or any other failure
            return ['city' => 'Unknown', 'country' => 'Unknown', 'isp' => 'Unknown'];
        }
    }
}
