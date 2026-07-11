<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Services\Auth\OtpService;
use App\Http\Services\Auth\RoleDetectionService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService,
        private readonly RoleDetectionService $roleDetectionService,
    ) {}

    // ── Landing ──────────────────────────────────────────────────────────────

    public function landing(): View
    {
        return view('landing');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', strtolower(trim($credentials['email'])))->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput(['email' => $credentials['email']]);
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Your account has been deactivated.'])->withInput();
        }

        if (!$user->email_verified_at) {
            // Resend OTP and redirect to verify
            $challenge = $this->otpService->createChallenge($user, 'register', 'email');
            $request->session()->put('otp_challenge_id', $challenge['challenge_id']);
            $request->session()->put('otp_user_id', $user->id);
            $request->session()->put('otp_purpose', 'register');
            return redirect()->route('otp.show')->with('info', 'Please verify your email first. A new OTP has been sent.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole($user->role);
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'university_id' => ['nullable', 'string', 'max:50'],
            'department'  => ['nullable', 'string', 'max:100'],
        ]);

        $email = strtolower(trim($payload['email']));

        try {
            $detectedRole = $this->roleDetectionService->detectUserRole($email) ?? 'student';
        } catch (\Throwable) {
            $detectedRole = 'student';
        }

        $user = User::create([
            'name'             => trim($payload['name']),
            'email'            => $email,
            'password'         => Hash::make($payload['password']),
            'phone'            => $payload['phone'] ?? null,
            'university_id'    => $payload['university_id'] ?? null,
            'department'       => $payload['department'] ?? null,
            'role'             => $detectedRole,
            'is_active'        => true,
            'email_verified_at' => null,
        ]);

        $challenge = $this->otpService->createChallenge($user, 'register', 'email');

        $request->session()->put('otp_challenge_id', $challenge['challenge_id']);
        $request->session()->put('otp_user_id', $user->id);
        $request->session()->put('otp_purpose', 'register');

        $flashData = ['success' => 'Account created! Please enter the OTP sent to your email.'];
        if (!empty($challenge['debug_otp'])) {
            $flashData['debug_otp'] = 'DEBUG OTP: ' . $challenge['debug_otp'];
        }

        return redirect()->route('otp.show')->with($flashData);
    }

    // ── OTP Verification ──────────────────────────────────────────────────────

    public function showOtp(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('otp_challenge_id')) {
            return redirect()->route('login')->with('error', 'No active OTP session. Please log in again.');
        }
        return view('auth.otp');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $challengeId = $request->session()->get('otp_challenge_id');
        $purpose     = $request->session()->get('otp_purpose', 'register');

        if (!$challengeId) {
            return redirect()->route('login')->with('error', 'Session expired. Please start again.');
        }

        $result = $this->otpService->verifyChallenge($challengeId, $request->input('otp'), $purpose);

        if (!$result['ok']) {
            return back()->withErrors(['otp' => $result['message']]);
        }

        /** @var \App\Models\User $user */
        $user = $result['user'];
        $user->forceFill(['email_verified_at' => now()])->save();

        $request->session()->forget(['otp_challenge_id', 'otp_user_id', 'otp_purpose']);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user->role)->with('success', 'Email verified! Welcome to ParKar.');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('student.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }
}
