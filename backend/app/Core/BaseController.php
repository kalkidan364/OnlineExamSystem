<?php

namespace App\Core;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelBaseController;
use App\Core\Traits\ApiResponseTrait;

abstract class BaseController extends LaravelBaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponseTrait;
}
