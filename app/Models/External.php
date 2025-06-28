<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $service_id
 * @property string $external_id
 * @property Service $service
 */
class External extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];


    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
