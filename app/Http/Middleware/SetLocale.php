<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            App::setLocale(Config::get('app.locale'));
        }

        // توحيد الجارد الافتراضي على admin عند تسجيل الدخول منه
        // حتى تعمل Gate/@can/auth()->user() بشكل صحيح في كل الداشبورد
        if (Auth::guard('admin')->check()) {
            Auth::shouldUse('admin');
        }

        return $next($request);
    }
}
