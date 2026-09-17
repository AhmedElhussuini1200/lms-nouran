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
    Route::get('attendance/scan/{token}', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::get('courses-events', [CourseController::class, 'events'])->name('courses.events');
    Route::resource('courses', CourseController::class)->middleware('owns');
    Route::resource('assignments', AssignmentController::class)->middleware('owns');
    Route::resource('exams', ExamController::class)->middleware('owns');
    Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->middleware('owns')->name('assignments.submit');
    Route::post('submissions/{submission}/grade', [AssignmentController::class, 'grade'])->middleware('owns')->name('submissions.grade');
    Route::post('submissions/{submission}/start-review', [AssignmentController::class, 'startReview'])->middleware('owns')->name('submissions.start-review');
    Route::post('exams/{exam}/submit', [ExamController::class, 'submit'])->middleware('owns')->name('exams.submit');
    Route::post('exams/{exam}/start', [ExamController::class, 'startAttempt'])->middleware('owns')->name('exams.start');
    // تفاعل: تقدم + تعليقات
    Route::post('videos/{video}/progress', [\App\Http\Controllers\Dashboard\EngagementController::class, 'progress'])->name('videos.progress');
    Route::get('{type}/{id}/comments', [\App\Http\Controllers\Dashboard\EngagementController::class, 'comments'])->where('type', 'video|course')->name('comments.index');
    Route::post('{type}/{id}/comments', [\App\Http\Controllers\Dashboard\EngagementController::class, 'storeComment'])->where('type', 'video|course')->name('comments.store');
    // إنجازات: شهادات + صدارة
    Route::get('certificates', [\App\Http\Controllers\Dashboard\AchievementController::class, 'certificates'])->name('certificates.index');
    Route::get('certificates/{certificate}/pdf', [\App\Http\Controllers\Dashboard\AchievementController::class, 'pdf'])->name('certificates.pdf');
    Route::get('certificates/verify/{code}', [\App\Http\Controllers\Dashboard\AchievementController::class, 'verify'])->name('certificates.verify');
    Route::get('leaderboard', [\App\Http\Controllers\Dashboard\AchievementController::class, 'leaderboard'])->name('leaderboard');
    // مركز التقارير
    Route::get('reports', [\App\Http\Controllers\Dashboard\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/grades.xlsx', [\App\Http\Controllers\Dashboard\ReportController::class, 'gradesExcel'])->name('reports.grades');
    Route::get('reports/attendance.xlsx', [\App\Http\Controllers\Dashboard\ReportController::class, 'attendanceExcel'])->name('reports.attendance');
    Route::get('reports/payments.xlsx', [\App\Http\Controllers\Dashboard\ReportController::class, 'paymentsExcel'])->name('reports.payments');
    Route::get('reports/engagement.pdf', [\App\Http\Controllers\Dashboard\ReportController::class, 'engagementPdf'])->name('reports.engagement');
    // دفع أونلاين
    Route::post('payments/{payment}/checkout', [\App\Http\Controllers\Dashboard\OnlinePaymentController::class, 'checkout'])->name('onlinepay.checkout');
    Route::post('payments/{payment}/confirm', [\App\Http\Controllers\Dashboard\OnlinePaymentController::class, 'confirm'])->name('onlinepay.confirm');
    Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Dashboard\OnlinePaymentController::class, 'receipt'])->name('onlinepay.receipt');
    // AI مساعد
    Route::get('ai', [\App\Http\Controllers\Dashboard\AiController::class, 'index'])->name('ai.index');
    Route::post('ai/ask', [\App\Http\Controllers\Dashboard\AiController::class, 'ask'])->name('ai.ask');
    Route::post('ai/quiz', [\App\Http\Controllers\Dashboard\AiController::class, 'quiz'])->name('ai.quiz');
    // لايف
    Route::post('courses/{course}/live/start', [\App\Http\Controllers\Dashboard\LiveController::class, 'start'])->middleware('owns')->name('live.start');
    Route::get('live/{course}', [\App\Http\Controllers\Dashboard\LiveController::class, 'room'])->name('live.room');
    Route::post('live/{course}/stop', [\App\Http\Controllers\Dashboard\LiveController::class, 'stop'])->name('live.stop');
    Route::post('live/{course}/message', [\App\Http\Controllers\Dashboard\LiveController::class, 'message'])->name('live.message');
    Route::get('live/{course}/feed', [\App\Http\Controllers\Dashboard\LiveController::class, 'feed'])->name('live.feed');
    // تحليلات
    Route::get('analytics/at-risk', [\App\Http\Controllers\Dashboard\AnalyticsController::class, 'atRisk'])->name('analytics.risk');
    Route::post('analytics/alert-parents', [\App\Http\Controllers\Dashboard\AnalyticsController::class, 'alertParents'])->name('analytics.alert');
    Route::get('analytics/parent/{student}', [\App\Http\Controllers\Dashboard\AnalyticsController::class, 'parentReport'])->name('analytics.parent');
    // واتساب بوت (webhook بدون auth admins — يتحقق بالمفتاح داخلياً)
    Route::post('whatsapp/inbound', [\App\Http\Controllers\Dashboard\WhatsappBotController::class, 'inbound'])->withoutMiddleware(['auth:admin'])->name('whatsapp.inbound');
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
