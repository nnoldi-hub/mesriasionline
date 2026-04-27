<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorAuthService;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    protected TwoFactorAuthService $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show 2FA settings page
     */
    public function index()
    {
        $user = auth()->user();
        $isEnabled = $this->twoFactorService->isEnabled($user);
        $recoveryCodes = $isEnabled ? $this->twoFactorService->getRecoveryCodes($user) : [];

        return view('security.two-factor', compact('isEnabled', 'recoveryCodes'));
    }

    /**
     * Enable 2FA - Step 1: Generate secret and QR code
     */
    public function enable(): JsonResponse
    {
        $user = auth()->user();

        if ($this->twoFactorService->isEnabled($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Autentificarea în doi pași este deja activată.',
            ], 400);
        }

        $data = $this->twoFactorService->enable($user);

        return response()->json([
            'success' => true,
            'secret' => $data['secret'],
            'qr_code_svg' => $data['qr_code_svg'],
            'message' => 'Scanează codul QR cu aplicația Google Authenticator.',
        ]);
    }

    /**
     * Confirm 2FA - Step 2: Verify code and activate
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $confirmed = $this->twoFactorService->confirm($user, $request->code);

        if ($confirmed) {
            AuditLog::log(AuditLog::ACTION_2FA_ENABLED, $user);

            $recoveryCodes = $this->twoFactorService->getRecoveryCodes($user);

            return response()->json([
                'success' => true,
                'message' => 'Autentificarea în doi pași a fost activată cu succes!',
                'recovery_codes' => $recoveryCodes,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Codul introdus nu este valid. Încearcă din nou.',
        ], 400);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|min:6',
        ]);

        $user = auth()->user();
        $disabled = $this->twoFactorService->disable($user, $request->code);

        if ($disabled) {
            AuditLog::log(AuditLog::ACTION_2FA_DISABLED, $user);

            return response()->json([
                'success' => true,
                'message' => 'Autentificarea în doi pași a fost dezactivată.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Codul introdus nu este valid.',
        ], 400);
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|min:6',
        ]);

        $user = auth()->user();
        
        if (!$this->twoFactorService->verify($user, $request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Codul introdus nu este valid.',
            ], 400);
        }

        $codes = $this->twoFactorService->regenerateRecoveryCodes($user);

        return response()->json([
            'success' => true,
            'recovery_codes' => $codes,
            'message' => 'Codurile de recuperare au fost regenerate.',
        ]);
    }

    /**
     * Verify 2FA code (for login challenge)
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|min:6',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $verified = $this->twoFactorService->verify($user, $request->code);

        if ($verified) {
            // Complete login
            Auth::login($user);
            $request->session()->regenerate();

            AuditLog::log(AuditLog::ACTION_LOGIN, $user);

            return response()->json([
                'success' => true,
                'redirect' => $this->getRedirectUrl($user),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Codul introdus nu este valid.',
        ], 400);
    }

    /**
     * Get redirect URL based on user role
     */
    protected function getRedirectUrl($user): string
    {
        return match($user->role) {
            'admin', 'superadmin' => route('admin.dashboard'),
            'specialist' => route('craftsman.dashboard'),
            default => route('home'),
        };
    }
}
