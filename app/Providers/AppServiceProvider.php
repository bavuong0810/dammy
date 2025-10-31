<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();
        Schema::defaultStringLength(191);
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $theme_option = json_decode(
            Cache::rememberForever('theme_option', function () {
                return json_encode(Setting::orderBy('updated', 'desc')->first());
            }),
            true
        );
        view()->share('getOptions', $theme_option);

        $settings = Cache::remember('business_settings', 86400, function () {
            return BusinessSetting::all();
        });
        view()->share('settings', $settings);
    }
}
