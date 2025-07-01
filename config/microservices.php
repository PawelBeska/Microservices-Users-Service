<?php

use App\Enums\ServiceEnum;

return [
    'name' => env('MICROSERVICE_NAME', 'microservice'),
    'host' => env('MICROSERVICE_HOST', 'localhost'),
    'port' => env('MICROSERVICE_PORT', '8000'),
    'service' => env('MICROSERVICE_SERVICE', 'users'),
    'service_enum' => ServiceEnum::class,
];
