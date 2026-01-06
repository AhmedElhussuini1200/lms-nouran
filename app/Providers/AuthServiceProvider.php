<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($admin, $ability) {
            // لو مش داخل كأدمن سيب بقية السيستم (policies/abilities) تشتغل عادي
            if (!Auth::guard('admin')->check()) {
                return null;
            }

            // الأدمن (type = admin) سوبر، يدخل على كل حاجة
            if ($admin->type === 'admin') {
                return true;
            }

            // لباقي الأنواع (teacher / student / parent) اعتمد على الـ abilities المرتبطة بالـ roles
            if ($admin->abilities()->pluck('name')->contains($ability)) {
                return true;
            }

            // رجّع null عشان لو فيه تعريفات تانية في Gate أو Policies تكمّل
            return null;
        });
    }
}
