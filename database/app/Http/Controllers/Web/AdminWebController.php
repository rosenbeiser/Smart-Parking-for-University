<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Payment;
use App\Models\ParkingApplication;
use App\Models\ParkingTicket;
use App\Models\Semester;
use App\Models\User;
use App\Support\NotificationPublisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminWebController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(): View
    {
        $applications = ParkingApplication::query()
            ->with(['semester:id,name', 'vehicle:id,plate_number', 'parkingTicket'])
            ->orderByDesc('created_at')
            ->get();

        $approvedCount  = $applications->where('status', 'approved')->count();
        $rejectedCount  = $applications->where('status', 'rejected')->count();
        $pendingCount   = $applications->where('status', 'pending')->count();
        $reviewedCount  = $approvedCount + $rejectedCount;

        $activeSemester = Semester::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $recentAuditLogs = AuditLog::query()
            ->with('application:id,applicant_name,status')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $pendingPayments = Payment::query()
            ->with(['user:id,name,email', 'application:id,applicant_name'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $overview = [
            'total_applications'   => $applications->count(),
            'pending'              => $pendingCount,
            'approved'             => $approvedCount,
            'rejected'             => $rejectedCount,
            'approval_rate'        => $reviewedCount > 0 ? round(($approvedCount / $reviewedCount) * 100, 1) : 0.0,
            'total_users'          => User::query()->count(),
            'pending_payments'     => Payment::query()->where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('overview', 'applications', 'activeSemester', 'recentAuditLogs', 'pendingPayments'));
    }

    // ── Applications ──────────────────────────────────────────────────────────

    public function applications(Request $request): View
    {
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));

        $query = ParkingApplication::query()
            ->with(['user:id,name,email,role', 'semester:id,name', 'vehicle:id,plate_number,vehicle_type', 'parkingTicket', 'payments'])
            ->orderByDesc('id');

        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                  ->orWhere('applicant_university_id', 'like', "%{$search}%")
                  ->orWhere('applicant_email', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('admin.applications.index', compact('applications', 'status', 'search'));
    }

    public function showApplication(ParkingApplication $application): View
    {
        $application->load([
            'user:id,name,email,university_id,role,department',
            'semester:id,name,start_date,end_date,semester_fee',
            'vehicle:id,plate_number,vehicle_type,brand,model,color,registration_number',
            'documents:id,document_type,file_path,is_verified,created_at',
            'parkingTicket',
            'aiAnalysis',
            'payments',
        ]);

        return view('admin.applications.show', compact('application'));
    }

    public function reviewApplication(Request $request, ParkingApplication $application): RedirectResponse
    {
        $payload = $request->validate([
            'status'        => ['required', 'in:approved,rejected'],
            'admin_comment' => ['nullable', 'string', 'max:2000'],
            'parking_slot'  => ['nullable', 'string', 'max:40'],
        ]);

        if ($payload['status'] === 'rejected' && empty(trim((string) ($payload['admin_comment'] ?? '')))) {
            return back()->withErrors(['admin_comment' => 'A comment is required when rejecting.'])->withInput();
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be reviewed.');
        }

        try {
            DB::transaction(function () use ($request, $application, $payload) {
                $application->forceFill([
                    'status'        => $payload['status'],
                    'admin_comment' => isset($payload['admin_comment']) ? trim((string) $payload['admin_comment']) : null,
                    'reviewed_by'   => Auth::id(),
                    'reviewed_at'   => now(),
                ])->save();

                if ($payload['status'] === 'approved') {
                    ParkingTicket::query()->firstOrCreate(
                        ['application_id' => $application->id],
                        [
                            'ticket_id'    => $this->generateUniqueTicketId(),
                            'issue_date'   => now(),
                            'parking_slot' => isset($payload['parking_slot']) ? trim((string) $payload['parking_slot']) : null,
                        ]
                    );
                }

                AuditLog::query()->create([
                    'admin_id'       => Auth::id(),
                    'application_id' => $application->id,
                    'action'         => $payload['status'] === 'approved' ? 'approved' : 'rejected',
                    'reason'         => isset($payload['admin_comment']) ? trim((string) $payload['admin_comment']) : null,
                    'created_at'     => now(),
                ]);
            });

            $statusLabel = $payload['status'] === 'approved' ? 'approved' : 'rejected';
            NotificationPublisher::createForUser(
                (int) $application->user_id,
                'Application ' . $statusLabel,
                "Your parking application #{$application->id} has been {$statusLabel}."
                . ($payload['status'] === 'rejected' && !empty($payload['admin_comment']) ? ' Reason: ' . $payload['admin_comment'] : '')
            );

            return redirect()->route('admin.applications')
                ->with('success', "Application #{$application->id} has been {$statusLabel}.");
        } catch (Throwable $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    // ── Document Viewing ──────────────────────────────────────────────────────

    public function viewDocument(Document $document): StreamedResponse
    {
        return $this->streamDocument($document, false);
    }

    public function downloadDocument(Document $document): StreamedResponse
    {
        return $this->streamDocument($document, true);
    }

    private function streamDocument(Document $document, bool $asAttachment): StreamedResponse
    {
        $disk = Storage::disk('public');
        $path = (string) $document->file_path;

        abort_unless($disk->exists($path), 404, 'Document not found.');

        $stream      = $disk->readStream($path);
        $mimeType    = $disk->mimeType($path) ?: 'application/octet-stream';
        $filename    = basename($path);
        $disposition = $asAttachment ? 'attachment' : 'inline';

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
        ]);
    }

    // ── Payment Management ────────────────────────────────────────────────────

    public function payments(Request $request): View
    {
        $status = trim((string) $request->query('status', ''));

        $query = Payment::query()
            ->with(['user:id,name,email', 'application:id,applicant_name,status', 'confirmedBy:id,name'])
            ->orderByDesc('created_at');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments', 'status'));
    }

    public function confirmPayment(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $payload = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payment->forceFill([
            'status'       => 'confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'notes'        => $payload['notes'] ?? null,
        ])->save();

        NotificationPublisher::createForUser(
            (int) $payment->user_id,
            'Payment confirmed',
            "Your payment (Transaction ID: {$payment->transaction_id}) has been confirmed. You can now download your parking permit."
        );

        return back()->with('success', 'Payment confirmed. User can now download their permit.');
    }

    public function rejectPayment(Request $request, Payment $payment): RedirectResponse
    {
        $payload = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payment->forceFill([
            'status' => 'failed',
            'notes'  => $payload['notes'] ?? 'Payment rejected by admin.',
        ])->save();

        NotificationPublisher::createForUser(
            (int) $payment->user_id,
            'Payment rejected',
            "Your payment (Transaction ID: {$payment->transaction_id}) has been rejected. Please contact the admin or resubmit your payment."
        );

        return back()->with('error_notice', 'Payment has been rejected and user notified.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function generateUniqueTicketId(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $candidate = 'PKT-' . now()->format('Ymd') . '-' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            if (!ParkingTicket::query()->where('ticket_id', $candidate)->exists()) {
                return $candidate;
            }
        }
        return 'PKT-' . now()->format('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }
}
