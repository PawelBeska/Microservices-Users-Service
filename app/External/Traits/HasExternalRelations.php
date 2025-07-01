<?php

namespace App\External\Traits;

use App\Enums\ServiceEnum;
use App\External\Models\Relations\ExternalRelation;

trait HasExternalRelations
{


    public function external(ServiceEnum $serviceEnum, string $localKey, string $table): ExternalRelation
    {
        return new ExternalRelation(
            $this->newQuery(),
            $this,
            $serviceEnum,
            $localKey,
            $table
        );
    }

}
