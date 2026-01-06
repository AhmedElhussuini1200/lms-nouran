<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\UserStatus;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() && auth()->user()->status == UserStatus::Inactive->value) {
            // Redirect or return an error if OTP is not null
            return response([
                'success' => false,
                'message' => __('Your account is not verified.'),
            ], 422);
        }

        return $next($request);
    }
}
