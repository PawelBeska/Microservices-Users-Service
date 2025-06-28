<?php

namespace App\External;

use App\Models\External;
use App\Models\Service;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $service_id
 * @property Service $service
 */
trait HasExternalRelations
{
    public function initializeHasExternalRelations(): void
    {
        $this->mergeCasts([
            'service' => config('microservices.service_enum'),
        ]);
    }

    public function externalRelation(): BelongsTo
    {
        return $this->belongs(External::class);
    }

}
