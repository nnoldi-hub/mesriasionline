<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Action constants
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_LOGIN_FAILED = 'login_failed';
    const ACTION_PASSWORD_RESET = 'password_reset';
    const ACTION_2FA_ENABLED = '2fa_enabled';
    const ACTION_2FA_DISABLED = '2fa_disabled';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_VIEW = 'view';
    const ACTION_APPROVE = 'approve';
    const ACTION_REJECT = 'reject';

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Log an action
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): self {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }

    /**
     * Get logs for a specific model
     */
    public static function forModel(Model $model): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('model_type', get_class($model))
            ->where('model_id', $model->getKey())
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get logs for a user
     */
    public static function forUser(int $userId): \Illuminate\Database\Eloquent\Builder
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get suspicious activity
     */
    public static function getSuspiciousActivity(int $hours = 24): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('created_at', '>=', now()->subHours($hours))
            ->where(function ($query) {
                $query->where('action', self::ACTION_LOGIN_FAILED)
                    ->orWhereIn('action', [self::ACTION_DELETE, self::ACTION_EXPORT]);
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get human readable description
     */
    public function getDescriptionAttribute(): string
    {
        $descriptions = [
            self::ACTION_LOGIN => 'S-a autentificat',
            self::ACTION_LOGOUT => 'S-a deconectat',
            self::ACTION_LOGIN_FAILED => 'Încercare eșuată de autentificare',
            self::ACTION_PASSWORD_RESET => 'A resetat parola',
            self::ACTION_2FA_ENABLED => 'A activat autentificarea în doi pași',
            self::ACTION_2FA_DISABLED => 'A dezactivat autentificarea în doi pași',
            self::ACTION_CREATE => 'A creat',
            self::ACTION_UPDATE => 'A actualizat',
            self::ACTION_DELETE => 'A șters',
            self::ACTION_EXPORT => 'A exportat',
            self::ACTION_IMPORT => 'A importat',
            self::ACTION_VIEW => 'A vizualizat',
            self::ACTION_APPROVE => 'A aprobat',
            self::ACTION_REJECT => 'A respins',
        ];

        $base = $descriptions[$this->action] ?? $this->action;
        
        if ($this->model_type) {
            $modelName = class_basename($this->model_type);
            $base .= " {$modelName}";
            if ($this->model_id) {
                $base .= " #{$this->model_id}";
            }
        }

        return $base;
    }
}
