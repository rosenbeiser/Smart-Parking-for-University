<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ParkingApplication;
use App\Models\ParkingTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermitController extends Controller
{
    /**
     * Show the parking permit for an approved application.
     * Available immediately after admin approval — no payment required.
     */
    public function show(ParkingTicket $ticket): View|RedirectResponse
    {
        $user = Auth::user();

        $ticket->load([
            'application.user:id,name,email,university_id,department,role',
            'application.semester:id,name,start_date,end_date',
            'application.vehicle:id,plate_number,vehicle_type,brand,model,color',
        ]);

        $application = $ticket->application;

        // Only the owner or admin can view
        if ($user->role !== 'admin' && $application->user_id !== $user->id) {
            abort(403);
        }

        return view('permits.permit', compact('ticket', 'application'));
    }

    /**
     * Download the permit as a PDF.
     * Available immediately after admin approval — no payment required.
     */
    public function download(ParkingTicket $ticket): Response|RedirectResponse
    {
        $user = Auth::user();

        $ticket->load([
            'application.user:id,name,email,university_id,department,role',
            'application.semester:id,name,start_date,end_date',
            'application.vehicle:id,plate_number,vehicle_type,brand,model,color',
        ]);

        $application = $ticket->application;

        if ($user->role !== 'admin' && $application->user_id !== $user->id) {
            abort(403);
        }

        $pdf = Pdf::loadView('permits.permit_pdf', compact('ticket', 'application'))
            ->setPaper('A4', 'portrait');

        return $pdf->download("ParKar-Permit-{$ticket->ticket_id}.pdf");
    }

    /**
     * Show permit by application ID (convenience route for students).
     */
    public function showByApplication(ParkingApplication $application): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $application->user_id !== $user->id) {
            abort(403);
        }

        if ($application->status !== 'approved' || !$application->parkingTicket) {
            return redirect()->route('student.applications')
                ->with('info', 'Your permit will be available once your application is approved.');
        }

        return $this->show($application->parkingTicket);
    }
}
