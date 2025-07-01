<?php

namespace App\Models;

use Database\Factories\ExternalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $service_id
 * @property string $external_id
 * @property Service $service
 * @method static ExternalFactory factory(int $count = 1)
 */
class External extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    public static function resolveOrCreate(string|int $externalId, array $service): int
    {
        return Cache::remember(
            sprintf('external:%s:%s', $service['name'], $externalId),
            now()->addMonth(),
            static fn() => static::query()->where('external_id', $externalId)
                ->whereHas('service', fn(Builder $query) => $query->where('name', $service['name']))
                ->firstOrCreate(
                    [
                        'external_id' => $externalId
                    ],
                    [
                        'service_id' => Service::query()->firstOrCreate(
                            [
                                'name' => $service['name'],
                            ],
                            $service
                        )->id
                    ]
                )->id
        );
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
