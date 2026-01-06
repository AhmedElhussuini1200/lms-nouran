<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Group;

use App\Http\Controllers\Dashboard\RoleController;

use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\TrashController;
use App\Http\Controllers\Dashboard\VideoController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\SettingController as DashboardSettingController;
use App\Http\Controllers\Dashboard\DashboardController;

// ADMIN->middleware(['auth:admin', 'role:admin'])
Route::prefix('dashboard')->name('admin.')->middleware(['auth:admin', 'role:admin'])->group(function () {

    // لوحة التحكم الرئيسية حسب نوع المستخدم (admin/teacher/student/parent)
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    /* begin Delete And restore */
    // Route::get('/contracts/{id}/last-item', ['ContractItemController@getLastItemNumber'])->name('dashboard.admin.contractItems.lastItem');


    // Route::delete("admins/delete-selected", "AdminController@deleteSelected");
    // Route::get("admins/restore-selected", "AdminController@restoreSelected");
    Route::resource('admins', AdminController::class);


    Route::get('profile-info', [ProfileController::class, 'profileInfo'])->name('admins.profile-info');
    Route::put('update-profile-info',  [ProfileController::class, 'updateProfileInfo'])->name('admins.update-profile-info');
    Route::put('update-profile-email',  [ProfileController::class, 'updateProfileEmail'])->name('admins.update-profile-email');
    Route::put('update-profile-password',  [ProfileController::class, 'updateProfilePassword'])->name('admins.update-profile-password');
    Route::get('/change-theme-mode/{mode}',  [ProfileController::class, 'changeThemeMode'])->name('admins.change-mode');
    Route::get('/language/{lang}', function (Request $request) {
        session()->put('locale', $request->lang);
        return redirect()->back();
    })->name('change-language');

    // Route::middleware(['set_locale'])->group(function () {
    //     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // });




    // Route::get('/offer/{user_id}', [OffersController::class, 'userOffers']);

    /**  ====================NOTIFICATIONS======================  **/

    Route::post('/save-token', [NotificationController::class, 'saveToken'])->name('save-token');
    Route::post('/send-notification', [NotificationController::class, 'sendNotification'])->name('send.notification');
    Route::get('notifications/{id}/mark_as_read', [NotificationController::class, 'markAsRead'])->name('notifications.mark_as_read');
    Route::get('notifications/{type}/load-more/{next}', [NotificationController::class, 'loadMore'])->name('notifications.load_more');
    Route::get('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_as_read');

    /**  ====================SETTINGS======================  **/
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('/', DashboardSettingController::class);
        Route::resource('roles', RoleController::class);
        Route::get('role/{role}/admins', [RoleController::class, 'admins'])
            ->name('roles.admins');

        Route::get('trash/{modelName?}', [TrashController::class, 'index'])->name('trash');
        Route::get('trash/{modelName}/{id}', [TrashController::class, 'restore']);
        Route::delete('trash/{modelName}/{id}', [TrashController::class, 'forceDelete']);
    });
});
