<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\TrashController;
use App\Http\Controllers\Dashboard\VideoController;
use App\Http\Controllers\Dashboard\CourseController;
use App\Http\Controllers\Dashboard\AssignmentController;
use App\Http\Controllers\Dashboard\ExamController;
use App\Http\Controllers\Dashboard\BrandingController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\StatusController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\AttendanceController;
use App\Http\Controllers\Dashboard\WhatsappController;
use App\Http\Controllers\Dashboard\WhatsappTemplateController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\SettingController as DashboardSettingController;
use App\Http\Controllers\Dashboard\DashboardController;

// كل المسجلين (admin/teacher/student/parent) يقدروا يفتحوا الداشبورد والفيديوهات
// إدارة المستخدمين والإعدادات محمية بـ role:admin + authorize داخل الكنترولر
Route::prefix('dashboard')->name('admin.')->middleware(['auth:admin'])->group(function () {

    // لوحة التحكم الرئيسية حسب نوع المستخدم (admin/teacher/student/parent)
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    /** ==================== LMS (Repository Pattern + owns middleware) =================== **/
    // العزل على مستوى الراوت: كل مدرس يشوف حاجته بس (AdminService-style guards تبقى كحماية إضافية)
    Route::resource('videos', VideoController::class)->middleware('owns');
    Route::get('courses/{course}/attendance', [AttendanceController::class, 'mark'])->middleware('owns')->name('attendance.mark');
    Route::post('courses/{course}/attendance', [AttendanceController::class, 'store'])->middleware('owns')->name('attendance.store');
    Route::get('courses-events', [CourseController::class, 'events'])->name('courses.events');
    Route::resource('courses', CourseController::class)->middleware('owns');
    Route::resource('assignments', AssignmentController::class)->middleware('owns');
    Route::resource('exams', ExamController::class)->middleware('owns');
    Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->middleware('owns')->name('assignments.submit');
    Route::post('submissions/{submission}/grade', [AssignmentController::class, 'grade'])->middleware('owns')->name('submissions.grade');
    Route::post('submissions/{submission}/start-review', [AssignmentController::class, 'startReview'])->middleware('owns')->name('submissions.start-review');
    Route::post('exams/{exam}/submit', [ExamController::class, 'submit'])->middleware('owns')->name('exams.submit');
    Route::post('exams/{exam}/questions', [ExamController::class, 'addQuestion'])->middleware('owns')->name('exams.questions.store');
    Route::delete('questions/{question}', [ExamController::class, 'deleteQuestion'])->middleware('owns')->name('questions.destroy');
    Route::post('results/{result}/grade', [ExamController::class, 'grade'])->middleware('owns')->name('results.grade');
    Route::get('branding', [BrandingController::class, 'index'])->name('branding.index');
    Route::put('branding', [BrandingController::class, 'update'])->name('branding.update');
    Route::get('payments/monthly-pdf', [PaymentController::class, 'monthlyPdf'])->name('payments.monthly-pdf');
    Route::resource('payments', PaymentController::class)->except(['show']);
    Route::get('statuses', [StatusController::class, 'index'])->name('statuses.index');
    Route::put('statuses/{status}', [StatusController::class, 'update'])->name('statuses.update');
    Route::get('whatsapp', [WhatsappController::class, 'index'])->name('whatsapp.index');
    Route::get('whatsapp/logs', [WhatsappController::class, 'logs'])->name('whatsapp.logs');
    Route::post('whatsapp/send', [WhatsappController::class, 'send'])->name('whatsapp.send');
    Route::get('whatsapp/templates', [WhatsappTemplateController::class, 'index'])->name('whatsapp.templates');
    Route::post('whatsapp/templates', [WhatsappTemplateController::class, 'store'])->name('whatsapp.templates.store');
    Route::put('whatsapp/templates/{template}', [WhatsappTemplateController::class, 'update'])->name('whatsapp.templates.update');
    Route::delete('whatsapp/templates/{template}', [WhatsappTemplateController::class, 'destroy'])->name('whatsapp.templates.destroy');
    Route::resource('roles', RoleController::class)->except(['show']);


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
    Route::get('notifications/{id}/mark_as_read', [NotificationController::class, 'markAsRead'])->name('notifications.mark_as_read');
    Route::get('notifications/{type}/load-more/{next}', [NotificationController::class, 'loadMore'])->name('notifications.load_more');
    Route::get('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_as_read');

    /**  ====================SETTINGS & TRASH======================  **/
    Route::get('settings', [DashboardSettingController::class, 'index'])->name('settings.index');

    Route::get('trash/{modelName?}', [TrashController::class, 'index'])->name('trash')->where('modelName', 'Admin');
    Route::get('trash/{modelName}/{id}', [TrashController::class, 'restore'])->where('modelName', 'Admin');
    Route::delete('trash/{modelName}/{id}', [TrashController::class, 'forceDelete'])->where('modelName', 'Admin');
});
