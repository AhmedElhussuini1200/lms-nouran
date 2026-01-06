<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // نستخدم نفس الـ guard اللي مسجّل عليه الأدمن
        $user = Auth::guard('admin')->user();

        if (!$user || $user->type !== $role) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
