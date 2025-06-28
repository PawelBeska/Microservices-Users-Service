<?php

namespace App\Models;

use App\Enums\ServiceEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property ServiceEnum $service
 * @property string $host
 * @property int $port
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Service extends Model
{
    use SoftDeletes;

    public $guarded = ['id'];

    protected $casts = [
        'host' => 'string',
        'port' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getCasts(): array
    {
        return array_merge($this->casts, [
            'service' => config('microservices.service_enum')
        ]);
    }
}
