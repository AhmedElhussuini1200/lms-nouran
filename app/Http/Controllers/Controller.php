<?php

namespace App\Http\Controllers;

use App\Traits\RespondsWithHttpStatus;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController; // مهم جدًا

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, RespondsWithHttpStatus;
}
