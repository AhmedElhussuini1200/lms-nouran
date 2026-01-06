<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:admins,email',
            'password' => 'required',
        ]);
        if (Auth::guard('admin')->attempt($credentials, $request->has('remember_me'))) {
            $request->session()->regenerate();
            $user = Auth::guard('admin')->user();

            // Check if blocked
            if ($user->is_blocked) {
                Auth::guard('admin')->logout();

                throw ValidationException::withMessages([
                    'email' => [__('Your account has been banned by the administration')],
                ]);
            }

            // توجيه حسب نوع المستخدم (admin / teacher / student / parent)
            switch ($user->type) {
                case 'admin':
                    $redirectTo = route('admin.index');
                    break;
                case 'teacher':
                case 'student':
                case 'parent':
                    // الكل يروح على /admin و DashboardService يختار الـ view حسب النوع
                    $redirectTo = route('admin.index');
                    break;
                default:
                    Auth::guard('admin')->logout();

                    throw ValidationException::withMessages([
                        'email' => [__('Your account type is not allowed to login')],
                    ]);
            }

            return response()->json(['url' => $redirectTo]);
        }

        throw ValidationException::withMessages([
            'password' => __('The password is incorrect'),
        ]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login-form');
    }
}
