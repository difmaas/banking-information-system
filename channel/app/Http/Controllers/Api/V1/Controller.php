<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithApiResponse;
use App\Http\Controllers\Controller as BaseController;

abstract class Controller extends BaseController
{
    use RespondsWithApiResponse;
}
