<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Dashboard\Auth\AdminAuthController;

// Redirect / to dashboard
// Route::get('/', function () {
//     if (Auth::check()) {
//         $userType = Auth::user()->type;
//         switch ($userType) {
//             case 'admin':
//                 return redirect()->route('admin.dashboard');
//             case 'consultant':
//                 return redirect()->route('consultant.dashboard');
//             case 'contractor':
//                 return redirect()->route('contractor.dashboard');
//             default:
//                 return redirect()->route('admin.dashboard');
//         }
//     }

//     return redirect()->route('admin.login-form');
// });

// ------------------ Admin Auth ------------------
require __DIR__ . '/dashboard.php';
Route::middleware('web')->group(function () {
    Route::get('/', function () {
        $user = Auth::guard('admin')->user();

        if (!$user) {
            return redirect()->route('admin.login-form');
        }

        switch ($user->type) {
            case 'admin':
                return redirect()->route('admin.index');
            case 'teacher':
            case 'student':
            case 'parent':
                // Dashboard موحّد تحت /admin حسب نوع المستخدم
                return redirect()->route('admin.index');
            default:
                Auth::guard('admin')->logout();
                session()->invalidate();
                session()->regenerateToken();
                return redirect()->route('admin.login-form');
        }
    });
});

Route::group(['namespace' => 'Dashboard\Auth', 'middleware' => 'set_locale'], function () {
    // صفحة الدخول الأساسية (نفس الصفحة لكل الأنواع)
    Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login-form');

    // نوفّر alias عام باسم 'login' عشان أي حاجة في لارفيل تتوقعه
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');

    Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// ------------------ Broadcast ------------------
Route::middleware('auth:sanctum')->group(function () {
    Broadcast::routes();
});

// ------------------ Catch-All Fallback ------------------
// Route::fallback(function () {
//     if (Auth::check()) {
//         $userType = Auth::user()->type;
//         switch ($userType) {
//             case 'admin':
//                 return redirect()->route('admin.dashboard');
//             case 'consultant':
//                 return redirect()->route('consultant.dashboard');
//             case 'contractor':
//                 return redirect()->route('contractor.dashboard');
//             default:
//                 return redirect()->route('admin.dashboard');
//         }
//     }

//     return redirect()->route('admin.login-form');
// });
