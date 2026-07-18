<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\Document;
use App\Models\Notification;
use App\Models\ParkingApplication;
use App\Models\Semester;
use App\Models\Vehicle;
use App\Support\NotificationPublisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;
use Illuminate\Support\Facades\Cookie;

class StudentWebController extends Controller
{
    private const DOCUMENT_FIELD_TO_TYPE = [
        'vehicle_registration_certificate' => 'registration',
        'driving_license'                  => 'license',
        'university_id_card'               => 'id_card',
        'vehicle_photo'                    => 'vehicle_photo',
    ];

    private const RENEWAL_NOTE_PREFIX = 'Renewal Meta:';

    private const STUDY_SEMESTERS = [
        '1.1', '1.2', '2.1', '2.2', '3.1', '3.2', '4.1', '4.2', '5.1', '5.2',
    ];

    public function __construct() {}

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(): View
    {
        $user = Auth::user();

        $activeSemester = Semester::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $applications = ParkingApplication::query()
            ->with(['semester:id,name,start_date,end_date', 'vehicle:id,plate_number,vehicle_type,brand,model', 'parkingTicket'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $overview = [
            'total'    => $applications->count(),
            'approved' => $applications->where('status', 'approved')->count(),
            'pending'  => $applications->where('status', 'pending')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        $latest = $applications->first();

        // ── Lab 12: External API via Guzzle (Open-Meteo — free, no key) ───────
        $weather = (new WeatherController())->getWeather();

        return view('student.dashboard', compact('user', 'activeSemester', 'overview', 'applications', 'latest', 'weather'));
    }

    // ── Applications ──────────────────────────────────────────────────────────

    public function applications(): View
    {
        $user = Auth::user();
        $applications = ParkingApplication::query()
            ->with(['semester:id,name', 'vehicle:id,plate_number,vehicle_type', 'parkingTicket', 'payments'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('student.applications', compact('applications'));
    }

    // ── Apply ─────────────────────────────────────────────────────────────────

    public function showApply(Request $request): View
    {
        $user = Auth::user();
        $semesters = Semester::query()->where('is_active', true)->orderByDesc('start_date')->get();
        $studySemesters = self::STUDY_SEMESTERS;
        $isTeacher = $user->role === 'teacher';

        // ── Lab 9: Cookies — read remembered vehicle type ─────────────────────
        // If the user has applied before, pre-fill their preferred vehicle type
        // from the cookie so they don't have to select it again.
        $preferredVehicleType = $request->cookie('preferred_vehicle_type');

        return view('student.apply', compact('user', 'semesters', 'studySemesters', 'isTeacher', 'preferredVehicleType'));
    }

    public function submitApply(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isTeacher = $user->role === 'teacher';

        $payload = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'aust_id'           => ['required', 'string', 'max:50'],
            'study_semester'    => $isTeacher
                ? ['nullable', 'string', 'max:50']
                : ['required', 'in:' . implode(',', self::STUDY_SEMESTERS)],
            'department'        => $isTeacher ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'],
            'academic_role'     => $isTeacher
                ? ['required', 'in:lecturer,professor,associate_professor,adjunct_professor']
                : ['nullable', 'string', 'max:80'],
            'email'             => ['required', 'email:rfc', 'max:255'],
            'contact_phone'     => ['required', 'string', 'max:20'],
            'vehicle_plate'     => ['required', 'string', 'max:20'],
            'vehicle_type'      => ['required', 'in:car,motorcycle,other'],
            'vehicle_model'     => ['required', 'string', 'max:120'],
            'vehicle_color'     => ['required', 'string', 'max:50'],
            'vehicle_brand'     => ['required', 'string', 'max:120'],
            'registration_number' => ['required', 'string', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:2000'],
            'nda_signed'        => ['required', 'accepted'],
            'documents.vehicle_registration_certificate' => ['required', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'documents.driving_license'                  => ['required', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'documents.university_id_card'               => ['required', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'documents.vehicle_photo'                    => ['required', 'file', 'mimetypes:image/jpeg,image/png', 'max:5120'],
        ]);

        $semester = $this->resolveSubmissionSemester();

        $storedPaths = [];

        try {
            DB::beginTransaction();

            $user->forceFill([
                'name'          => trim((string) $payload['name']),
                'phone'         => trim((string) $payload['contact_phone']),
                'university_id' => trim((string) $payload['aust_id']),
                'department'    => $isTeacher ? trim((string) $payload['department']) : $user->department,
            ])->save();

            $vehicle = Vehicle::query()->create([
                'user_id'             => $user->id,
                'plate_number'        => strtoupper(trim((string) $payload['vehicle_plate'])),
                'vehicle_type'        => $payload['vehicle_type'],
                'brand'               => trim((string) $payload['vehicle_brand']),
                'model'               => trim((string) $payload['vehicle_model']),
                'color'               => trim((string) $payload['vehicle_color']),
                'registration_number' => trim((string) $payload['registration_number']),
            ]);

            $notesLines = [];
            if (!empty($payload['department']))   $notesLines[] = 'Department: ' . $payload['department'];
            if (!empty($payload['academic_role'])) $notesLines[] = 'Academic Role: ' . str_replace('_', ' ', $payload['academic_role']);
            if (!empty($payload['study_semester'])) $notesLines[] = 'Study Semester: ' . $payload['study_semester'];
            if (!empty($payload['notes']))         $notesLines[] = 'Notes: ' . $payload['notes'];

            $application = ParkingApplication::query()->create([
                'user_id'                  => $user->id,
                'semester_id'              => $semester->id,
                'vehicle_id'               => $vehicle->id,
                'status'                   => 'pending',
                'register_as'              => (string) $user->role,
                'applicant_name'           => trim((string) $payload['name']),
                'applicant_university_id'  => trim((string) $payload['aust_id']),
                'applicant_email'          => strtolower(trim((string) $payload['email'])),
                'applicant_phone'          => trim((string) $payload['contact_phone']),
                'notes'                    => implode(PHP_EOL, $notesLines),
                'nda_signed'               => true,
            ]);

            foreach (self::DOCUMENT_FIELD_TO_TYPE as $field => $documentType) {
                $file   = $request->file("documents.$field");
                $path   = $file->store("parking-documents/{$user->id}", 'public');
                $storedPaths[] = $path;

                $doc = Document::query()->create([
                    'user_id'       => $user->id,
                    'document_type' => $documentType,
                    'file_path'     => $path,
                    'is_verified'   => false,
                ]);

                ApplicationDocument::query()->create([
                    'application_id' => $application->id,
                    'document_id'    => $doc->id,
                    'created_at'     => now(),
                ]);
            }

            DB::commit();

            NotificationPublisher::createForUser($user->id, 'Application submitted', "Your parking application #{$application->id} has been submitted and is now pending review.");
            NotificationPublisher::createForRole('admin', 'New parking application', "{$user->name} submitted parking application #{$application->id} for review.");

            // ── Lab 9: Cookies — remember preferred vehicle type (7-day cookie) ────
            $vehicleTypeCookie = cookie('preferred_vehicle_type', $payload['vehicle_type'], 60 * 24 * 7);

            return redirect()
                ->route('student.applications')
                ->with('success', "Application #{$application->id} submitted successfully! You will be notified when reviewed.")
                ->withCookie($vehicleTypeCookie);
        } catch (Throwable $e) {
            DB::rollBack();
            foreach ($storedPaths as $p) {
                Storage::disk('public')->delete($p);
            }
            Log::error('Application submission failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to submit application. Please try again.');
        }
    }

    // ── Renew ─────────────────────────────────────────────────────────────────

    public function showRenew(ParkingApplication $application): View|RedirectResponse
    {
        $user = Auth::user();
        if ($application->user_id !== $user->id) {
            abort(403);
        }
        if ($application->status !== 'approved') {
            return redirect()->route('student.applications')->with('error', 'Only approved applications can be renewed.');
        }
        $application->load(['vehicle', 'semester', 'documents']);
        return view('student.renew', compact('application'));
    }

    public function submitRenew(Request $request, ParkingApplication $application): RedirectResponse
    {
        $user = Auth::user();
        if ($application->user_id !== $user->id) abort(403);
        if ($application->status !== 'approved') {
            return redirect()->route('student.applications')->with('error', 'Only approved applications can be renewed.');
        }

        $payload = $request->validate([
            'acknowledged' => ['required', 'accepted'],
            'review_note'  => ['nullable', 'string', 'max:2000'],
        ]);

        $semester = $this->resolveSubmissionSemester();

        try {
            DB::beginTransaction();

            $renewalApplication = ParkingApplication::query()->create([
                'user_id'                 => $user->id,
                'semester_id'             => $semester->id,
                'vehicle_id'              => $application->vehicle_id,
                'status'                  => 'pending',
                'register_as'             => (string) $user->role,
                'applicant_name'          => (string) $application->applicant_name,
                'applicant_university_id' => (string) $application->applicant_university_id,
                'applicant_email'         => (string) $application->applicant_email,
                'applicant_phone'         => (string) $application->applicant_phone,
                'notes'                   => self::RENEWAL_NOTE_PREFIX . PHP_EOL
                    . "source_application_id={$application->id}" . PHP_EOL
                    . 'renewal_sequence=1' . PHP_EOL
                    . 'document_mode=keep' . PHP_EOL
                    . (isset($payload['review_note']) && trim($payload['review_note']) !== ''
                        ? 'review_note=' . trim($payload['review_note'])
                        : ''),
                'nda_signed' => (bool) $application->nda_signed,
            ]);

            // Attach existing documents
            $application->load('documents');
            foreach ($application->documents as $document) {
                ApplicationDocument::query()->create([
                    'application_id' => $renewalApplication->id,
                    'document_id'    => $document->id,
                    'created_at'     => now(),
                ]);
            }

            DB::commit();

            NotificationPublisher::createForUser($user->id, 'Renewal submitted', "Your renewal request #{$renewalApplication->id} has been submitted.");
            NotificationPublisher::createForRole('admin', 'New renewal request', "{$user->name} submitted renewal #{$renewalApplication->id}.");

            return redirect()->route('student.applications')->with('success', "Renewal application #{$renewalApplication->id} submitted.");
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit renewal. Please try again.');
        }
    }

    // ── Documents ─────────────────────────────────────────────────────────────

    public function documents(): View
    {
        $user = Auth::user();
        $documents = Document::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();
        return view('student.documents', compact('documents'));
    }

    // ── Vehicles ──────────────────────────────────────────────────────────────

    public function vehicles(): View
    {
        $user = Auth::user();
        $vehicles = Vehicle::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();
        return view('student.vehicles', compact('vehicles'));
    }

    // ── Profile ───────────────────────────────────────────────────────────────

    public function profile(): View
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $payload = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'university_id' => ['nullable', 'string', 'max:50'],
            'department'   => ['nullable', 'string', 'max:100'],
        ]);

        $user->forceFill([
            'name'          => trim($payload['name']),
            'phone'         => $payload['phone'] ?? $user->phone,
            'university_id' => $payload['university_id'] ?? $user->university_id,
            'department'    => $payload['department'] ?? $user->department,
        ])->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Notifications ─────────────────────────────────────────────────────────

    public function notifications(): View
    {
        $user = Auth::user();
        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        // Mark unread as read
        Notification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return view('student.notifications', compact('notifications'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolveSubmissionSemester(): Semester
    {
        $semester = Semester::query()
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->first();

        if ($semester) {
            return $semester;
        }

        $year = (int) now()->format('Y');
        return Semester::query()->create([
            'name'          => "Auto Semester {$year}",
            'start_date'    => "{$year}-01-01",
            'end_date'      => "{$year}-12-31",
            'vehicle_quota' => 1,
            'semester_fee'  => 0,
            'is_active'     => true,
        ]);
    }

    private function calculateRiskScore(array $resultsByField): float
    {
        if ($resultsByField === []) return 0.0;
        $totalRisk = collect($resultsByField)->sum(function (array $result): float {
            $confidenceRisk  = 1 - max(0, min(1, (float) $result['confidence']));
            $issueRisk       = min(0.35, count($result['issues']) * 0.08);
            $manualReviewRisk = !empty($result['error']) ? 0.2 : 0.0;
            return min(1, $confidenceRisk + $issueRisk + $manualReviewRisk);
        });
        return round($totalRisk / count($resultsByField), 4);
    }
}
