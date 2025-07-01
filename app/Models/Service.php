<?php

namespace App\Models;

use App\Enums\ServiceEnum;
use Carbon\Carbon;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property ServiceEnum $service
 * @property string $name
 * @property string $host
 * @property int $port
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @method static ServiceFactory factory(int $count = 1)
 */
class Service extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = [
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
