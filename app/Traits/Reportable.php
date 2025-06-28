<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait Reportable
{
    public function reportError(Exception $exception): void
    {
        Log::error(
            $exception->getMessage(),
            [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]
        );
    }

}
