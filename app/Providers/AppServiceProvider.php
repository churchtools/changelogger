<?php

namespace App\Providers;

use App\ChangeloggerConfig;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChangeloggerConfig::class, function ($app) {
            return new ChangeloggerConfig(config('changelogger.directory'));
        });
    }
}
