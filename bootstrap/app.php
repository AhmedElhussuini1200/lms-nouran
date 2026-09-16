<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // middlewares aliases
        $middleware->alias([
            'set_locale' => \App\Http\Middleware\SetLocale::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'owns' => \App\Http\Middleware\EnsureOwnsContent::class,
        ]);
        // اللغة من السيشن على كل صفحات الويب (عربي/إنجليزي)
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // شبكة أمان: أي خطأ قاعدة بيانات يرجع رسالة عربية مفهومة بدل صفحة SQL
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if (app()->isProduction() || $request->is('dashboard/*') || $request->is('admin/*')) {
                $message = __('حدث خطأ أثناء حفظ البيانات — تحقق من عدم التكرار وحاول مجدداً');

                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $message = __('هذا العنصر مسجل من قبل — لا يمكن التكرار');
                } elseif (str_contains($e->getMessage(), 'a foreign key constraint fails')) {
                    $message = __('لا يمكن إتمام العملية لوجود بيانات مرتبطة');
                }

                \Illuminate\Support\Facades\Log::warning('QueryException: ' . $e->getMessage());

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 422);
                }

                return redirect()->back()->withInput()->with('error_message', $message);
            }
        });
    })->create();
