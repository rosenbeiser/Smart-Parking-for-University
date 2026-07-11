<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ParkingApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Show the payment page for an approved application.
     * Payment is only allowed after admin approval.
     */
    public function show(ParkingApplication $application): View|RedirectResponse
    {
        $user = Auth::user();

        // Authorization: only the owner can pay
        if ($application->user_id !== $user->id) {
            abort(403);
        }

        // Must be approved
        if ($application->status !== 'approved') {
            return redirect()->route('student.applications')
                ->with('error', 'Payment is only available for approved applications.');
        }

        $application->load(['semester:id,name,semester_fee', 'vehicle:id,plate_number,vehicle_type,brand', 'parkingTicket']);

        // Check if payment already exists
        $existingPayment = Payment::query()
            ->where('application_id', $application->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if ($existingPayment && $existingPayment->isConfirmed()) {
            return redirect()->route('permit.show', $application->parkingTicket)
                ->with('info', 'Payment already confirmed. You can download your permit.');
        }

        $amount = $application->semester?->semester_fee ?? 0;

        // BKash and Nagad merchant numbers (configurable via env)
        $bkashNumber = config('services.payment.bkash_number', '01XXXXXXXXX');
        $nagadNumber = config('services.payment.nagad_number', '01XXXXXXXXX');

        return view('payment.index', compact('application', 'amount', 'bkashNumber', 'nagadNumber', 'existingPayment'));
    }

    /**
     * Submit a payment transaction ID for admin confirmation.
     */
    public function submit(Request $request, ParkingApplication $application): RedirectResponse
    {
        $user = Auth::user();

        if ($application->user_id !== $user->id) abort(403);
        if ($application->status !== 'approved') {
            return redirect()->route('student.applications')
                ->with('error', 'Payment is only available for approved applications.');
        }

        $payload = $request->validate([
            'method'         => ['required', 'in:bkash,nagad'],
            'transaction_id' => ['required', 'string', 'min:6', 'max:100', 'unique:payments,transaction_id'],
        ]);

        $amount = $application->semester?->semester_fee ?? 0;

        Payment::query()->create([
            'application_id' => $application->id,
            'user_id'        => $user->id,
            'method'         => $payload['method'],
            'transaction_id' => strtoupper(trim($payload['transaction_id'])),
            'amount'         => $amount,
            'status'         => 'pending',
        ]);

        return redirect()->route('student.applications')
            ->with('success', 'Payment submitted! Awaiting admin confirmation. You will be notified once confirmed.');
    }
}
