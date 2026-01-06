<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Middleware\SetLocale;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(
            \App\Repositories\Dashboard\Contracts\AdminRepositoryInterface::class,
            \App\Repositories\Dashboard\Eloquent\AdminRepository::class
        );



        $this->app->bind(
            \App\Repositories\Dashboard\Contracts\ProfileRepositoryInterface::class,
            \App\Repositories\Dashboard\Eloquent\ProfileRepository::class
        );
        $this->app->bind(
            \App\Repositories\Dashboard\Contracts\NotificationRepositoryInterface::class,
            \App\Repositories\Dashboard\Eloquent\NotificationRepository::class
        );
        $this->app->bind(
            \App\Repositories\Dashboard\Contracts\RoleRepositoryInterface::class,
            \App\Repositories\Dashboard\Eloquent\RoleRepository::class
        );

        $this->app->bind(
            \App\Repositories\Dashboard\Contracts\SettingRepositoryInterface::class,
            \App\Repositories\Dashboard\Eloquent\SettingRepository::class
        );
        $this->app->bind(
            \App\Repositories\Dashboard\Contracts\TrashRepositoryInterface::class,
            \App\Repositories\Dashboard\Eloquent\TrashRepository::class
        );
        $this->app->bind(
            \App\Repositories\Dashboard\Contracts\DashboardRepositoryInterface::class,
            \App\Repositories\Dashboard\Eloquent\DashboardRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        View::composer('dashboard.partials.master', function ($view) {
            $user = auth('admin')->user();
            if ($user) {
                $unreadNotifications = $user->unreadNotifications();
                $allNotifications    = $user->notifications();
                $view->with(['unreadNotifications' => $unreadNotifications, "allNotifications" => $allNotifications]);
            } else {
                $view->with(['unreadNotifications' => collect(), "allNotifications" => collect()]);
            }
        });
        View::composer('dashboard.partials.aside', function ($view) {
            // $winners = Winner::with('user')->limit(7)->get()->pluck('user')->filter(function ($user) {
            //     return !empty($user); // filters out null or empty user objects
            // })->select('id', 'name')->unique();
            // $view->with(['winners' => $winners]);
        });
    }
}
