<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ParKar Parking Permit — {{ $ticket->ticket_id }}</title>
    <style>
        @page { margin: 20mm 18mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1C1917; background: white; }

        .permit-outer { border: 3px solid #F97316; border-radius: 10px; overflow: hidden; }

        /* Header */
        .header { background: #EA580C; color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; }
        .header-left-title { font-size: 9px; letter-spacing: 1px; text-transform: uppercase; opacity: .8; margin-bottom: 4px; }
        .header-left-main  { font-size: 20px; font-weight: bold; }
        .header-left-sub   { font-size: 9px; opacity: .85; margin-top: 3px; }
        .header-right      { text-align: right; }
        .header-right-label{ font-size: 8px; opacity: .7; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .ticket-id { background: rgba(255,255,255,.15); padding: 6px 12px; border-radius: 5px; font-size: 13px; font-weight: bold; letter-spacing: 1px; }

        /* Status banner */
        .status-bar { background: #ECFDF5; border-bottom: 2px solid #A7F3D0; padding: 9px 24px; display: flex; justify-content: space-between; align-items: center; }
        .status-text { font-size: 11px; font-weight: bold; color: #065F46; }
        .issued-text { font-size: 9px; color: #047857; }

        /* Body */
        .body { padding: 18px 24px; }
        .cols { display: flex; gap: 24px; }
        .col { flex: 1; }
        .section-label { font-size: 8px; font-weight: bold; color: #F97316; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 12px; }
        .field { margin-bottom: 10px; }
        .field-label { font-size: 8px; color: #9CA3AF; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 2px; }
        .field-value { font-size: 12px; font-weight: bold; color: #1C1917; }
        .plate-box { font-size: 16px; font-weight: bold; letter-spacing: 2px; border: 2px solid #E5E7EB; padding: 5px 10px; border-radius: 4px; display: inline-block; background: #F9FAFB; margin-top: 3px; }

        /* Info Strip */
        .strip { background: #FFF7ED; border-top: 1px solid #FED7AA; padding: 12px 24px; display: flex; gap: 32px; }
        .strip-item-label { font-size: 8px; color: #EA580C; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 3px; }
        .strip-item-value { font-size: 11px; font-weight: bold; color: #1C1917; }
        .strip-item-sub   { font-size: 9px; color: #6B7280; margin-top: 1px; }

        /* Divider */
        .divider { border: none; border-top: 1px dashed #E5E7EB; margin: 0 24px; }

        /* Footer */
        .footer { padding: 10px 24px; display: flex; justify-content: space-between; align-items: center; }
        .footer-note { font-size: 8px; color: #9CA3AF; max-width: 380px; line-height: 1.5; }
        .footer-stamp { text-align: right; font-size: 8px; color: #9CA3AF; }

        /* Watermark */
        .watermark { text-align: center; padding: 6px; font-size: 9px; color: #D1D5DB; letter-spacing: 2px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="permit-outer">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="header-left-title">Ahsanullah University of Science & Technology</div>
                <div class="header-left-main">ParKar System — Parking Permit</div>
                <div class="header-left-sub">AI-Assisted University Parking Permission System</div>
            </div>
            <div class="header-right">
                <div class="header-right-label">Ticket ID</div>
                <div class="ticket-id">{{ $ticket->ticket_id }}</div>
            </div>
        </div>

        <!-- Status -->
        <div class="status-bar">
            <div class="status-text">✓ PERMIT VALID — AUTHORIZED ENTRY</div>
            <div class="issued-text">Issued: {{ $ticket->issue_date?->format('d M Y') ?? now()->format('d M Y') }}</div>
        </div>

        <!-- Main Body -->
        <div class="body">
            <div class="cols">
                <!-- Applicant -->
                <div class="col">
                    <div class="section-label">Permit Holder</div>
                    <div class="field">
                        <div class="field-label">Full Name</div>
                        <div class="field-value">{{ $application->applicant_name }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">University ID</div>
                        <div class="field-value">{{ $application->applicant_university_id }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Role</div>
                        <div class="field-value">{{ ucfirst($application->register_as ?? $application->user?->role ?? '—') }}</div>
                    </div>
                    @if($application->user?->department)
                    <div class="field">
                        <div class="field-label">Department</div>
                        <div class="field-value">{{ $application->user->department }}</div>
                    </div>
                    @endif
                    <div class="field">
                        <div class="field-label">Contact</div>
                        <div class="field-value">{{ $application->applicant_phone ?? '—' }}</div>
                    </div>
                </div>

                <!-- Vehicle -->
                <div class="col">
                    <div class="section-label">Vehicle Details</div>
                    @if($application->vehicle)
                    <div class="field">
                        <div class="field-label">License Plate</div>
                        <div class="plate-box">{{ $application->vehicle->plate_number }}</div>
                    </div>
                    <div class="field" style="margin-top:10px;">
                        <div class="field-label">Make & Model</div>
                        <div class="field-value">{{ $application->vehicle->brand }} {{ $application->vehicle->model }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Color</div>
                        <div class="field-value">{{ ucfirst($application->vehicle->color) }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Type</div>
                        <div class="field-value">{{ ucfirst($application->vehicle->vehicle_type) }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Registration No.</div>
                        <div class="field-value" style="font-size:10px;">{{ $application->vehicle->registration_number }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <hr class="divider">

        <!-- Info Strip -->
        <div class="strip">
            <div>
                <div class="strip-item-label">Semester</div>
                <div class="strip-item-value">{{ $application->semester?->name ?? 'N/A' }}</div>
                @if($application->semester)
                <div class="strip-item-sub">
                    {{ \Carbon\Carbon::parse($application->semester->start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($application->semester->end_date)->format('d M Y') }}
                </div>
                @endif
            </div>
            <div>
                <div class="strip-item-label">Parking Slot</div>
                <div class="strip-item-value">{{ $ticket->parking_slot ?? 'General Area' }}</div>
            </div>
            <div>
                <div class="strip-item-label">Email</div>
                <div class="strip-item-value" style="font-size:10px;">{{ $application->applicant_email }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-note">
                This permit is issued by the AUST ParKar System and is valid only for the semester indicated above.
                The permit holder must present this document to security personnel on request.
                Misuse of this permit may result in permanent revocation of parking privileges.
            </div>
            <div class="footer-stamp">
                ParKar System · AUST<br>
                Generated: {{ now()->format('d M Y, h:i A') }}<br>
                Application #{{ $application->id }}
            </div>
        </div>

        <div class="watermark">· · · · · · official parking permit · · · · · ·</div>
    </div>
</body>
</html>
