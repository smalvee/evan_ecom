<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share website settings with every view (single cached read per request).
        View::composer('*', function ($view) {
            static $data = null;

            if ($data === null) {
                $data = [
                    'settings' => Setting::siteSettings(),
                    'socialLinks' => Setting::socialLinks(),
                ];
            }

            $view->with($data);
        });
    }
}
