<?php

namespace App\Models;

use App\Enums\VerificationTokenTypeEnum;
use Database\Factories\VerificationTokenFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $user_id
 * @property string $token
 * @property VerificationTokenTypeEnum $type
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read  User $user
 * @method static Builder|VerificationToken isValid(string $token, VerificationTokenTypeEnum $type)
 * @method static VerificationTokenFactory factory(int $count = 1)
 */
class VerificationToken extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public $casts = [
        'type' => VerificationTokenTypeEnum::class,
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeIsValid(Builder $query, string $token, VerificationTokenTypeEnum $type): Builder
    {
        return $query->where('token', $token)
            ->where('type', $type)
            ->where(fn(Builder $query) => $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now())
            );
    }

}
