<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // لو المستخدم admin, منع الوصول لصفحة login
                if ($user && $user->type === 'admin') {
                    return redirect()->route('admin.admins.index');
                }



                // أي نوع آخر
                return redirect('/');
            }
        }

        return $next($request);
    }
}
