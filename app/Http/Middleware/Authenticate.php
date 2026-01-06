<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request)
    {
        if ($request->expectsJson()) {
            return abort(401);
        }

        // أي مستخدم غير مسجل دخول يروح للـ login
        return route('admin.login-form'); // أو login عام لو عندك
    }
}
