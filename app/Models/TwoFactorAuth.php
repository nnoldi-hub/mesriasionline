<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorAuth extends Model
{
    use HasFactory;

    protected $table = 'two_factor_auth';

    protected $fillable = [
        'user_id',
        'secret',
        'recovery_codes',
        'enabled',
        'confirmed_at',
    ];

    protected $casts = [
        'recovery_codes' => 'encrypted:array',
        'secret' => 'encrypted',
        'enabled' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    protected $hidden = [
        'secret',
        'recovery_codes',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if 2FA is fully enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && $this->confirmed_at !== null;
    }

    /**
     * Generate recovery codes
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4)));
        }
        
        $this->recovery_codes = $codes;
        $this->save();
        
        return $codes;
    }

    /**
     * Use a recovery code
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->recovery_codes ?? [];
        $key = array_search($code, $codes);
        
        if ($key !== false) {
            unset($codes[$key]);
            $this->recovery_codes = array_values($codes);
            $this->save();
            return true;
        }
        
        return false;
    }

    /**
     * Get remaining recovery codes count
     */
    public function remainingRecoveryCodes(): int
    {
        return count($this->recovery_codes ?? []);
    }
}
