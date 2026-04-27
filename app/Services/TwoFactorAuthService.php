<?php

namespace App\Services;

use App\Models\TwoFactorAuth;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class TwoFactorAuthService
{
    protected Google2FA $google2fa;
    protected string $appName;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->appName = config('app.name', 'Fixacasa');
    }

    /**
     * Enable 2FA for user (first step - generate secret)
     */
    public function enable(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();

        $twoFactor = TwoFactorAuth::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => $secret,
                'enabled' => false,
                'confirmed_at' => null,
            ]
        );

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            $this->appName,
            $user->email,
            $secret
        );

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'qr_code_svg' => $this->generateQrCodeSvg($qrCodeUrl),
        ];
    }

    /**
     * Confirm 2FA setup with verification code
     */
    public function confirm(User $user, string $code): bool
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();

        if (!$twoFactor || !$twoFactor->secret) {
            return false;
        }

        $valid = $this->google2fa->verifyKey($twoFactor->secret, $code);

        if ($valid) {
            $twoFactor->update([
                'enabled' => true,
                'confirmed_at' => now(),
            ]);
            
            // Generate recovery codes
            $twoFactor->generateRecoveryCodes();
            
            return true;
        }

        return false;
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(User $user, string $code): bool
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)
            ->where('enabled', true)
            ->first();

        if (!$twoFactor) {
            return true; // 2FA not enabled
        }

        // Try as regular TOTP code
        if ($this->google2fa->verifyKey($twoFactor->secret, $code)) {
            return true;
        }

        // Try as recovery code
        return $twoFactor->useRecoveryCode($code);
    }

    /**
     * Disable 2FA for user
     */
    public function disable(User $user, string $code): bool
    {
        // Verify code before disabling
        if (!$this->verify($user, $code)) {
            return false;
        }

        TwoFactorAuth::where('user_id', $user->id)->delete();
        return true;
    }

    /**
     * Check if user has 2FA enabled
     */
    public function isEnabled(User $user): bool
    {
        return TwoFactorAuth::where('user_id', $user->id)
            ->where('enabled', true)
            ->whereNotNull('confirmed_at')
            ->exists();
    }

    /**
     * Get recovery codes
     */
    public function getRecoveryCodes(User $user): array
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();
        return $twoFactor?->recovery_codes ?? [];
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(User $user): array
    {
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();
        
        if (!$twoFactor) {
            return [];
        }

        return $twoFactor->generateRecoveryCodes();
    }

    /**
     * Generate QR code as SVG
     */
    protected function generateQrCodeSvg(string $url): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            
            $writer = new Writer($renderer);
            return $writer->writeString($url);
        } catch (\Exception $e) {
            // Fallback to URL if QR generation fails
            return '';
        }
    }
}
