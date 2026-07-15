<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ParkingTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermitController extends Controller
{
    /**
     * Show the permit page for a confirmed-payment ticket.
     */
    public function show(ParkingTicket $ticket): View|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        $ticket->load([
            'application.user:id,name,email,university_id,department,role',
            'application.semester:id,name,start_date,end_date',
            'application.vehicle:id,plate_number,vehicle_type,brand,model,color',
            'application.payments',
        ]);

        $application = $ticket->application;

        // Only the owner or admin can view
        if ($user->role !== 'admin' && $application->user_id !== $user->id) {
            abort(403);
        }

        // Check payment confirmation (unless semester fee is 0)
        $semesterFee = $application->semester?->semester_fee ?? 0;
        if ($semesterFee > 0) {
            $confirmed = Payment::query()
                ->where('application_id', $application->id)
                ->where('status', 'confirmed')
                ->exists();

            if (!$confirmed && $user->role !== 'admin') {
                return redirect()->route('payment.show', $application->id)
                    ->with('info', 'Please complete payment to access your permit.');
            }
        }

        return view('permits.permit', compact('ticket', 'application'));
    }

    /**
     * Download the permit as a PDF.
     */
    public function download(ParkingTicket $ticket): Response|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        $ticket->load([
            'application.user:id,name,email,university_id,department,role',
            'application.semester:id,name,start_date,end_date',
            'application.vehicle:id,plate_number,vehicle_type,brand,model,color',
            'application.payments',
        ]);

        $application = $ticket->application;

        if ($user->role !== 'admin' && $application->user_id !== $user->id) {
            abort(403);
        }

        // Payment check
        $semesterFee = $application->semester?->semester_fee ?? 0;
        if ($semesterFee > 0 && $user->role !== 'admin') {
            $confirmed = Payment::query()
                ->where('application_id', $application->id)
                ->where('status', 'confirmed')
                ->exists();

            if (!$confirmed) {
                return redirect()->route('payment.show', $application->id)
                    ->with('info', 'Please complete payment to download your permit.');
            }
        }

        $pdf = Pdf::loadView('permits.permit_pdf', compact('ticket', 'application'))
            ->setPaper('A4', 'portrait');

        return $pdf->download("ParKar-Permit-{$ticket->ticket_id}.pdf");
    }
}
