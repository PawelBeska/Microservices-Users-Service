<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Traits\Reportable;

abstract class Controller
{
    use ApiResponse;
    use Reportable;
}
