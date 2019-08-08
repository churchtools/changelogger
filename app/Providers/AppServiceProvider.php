<?php

namespace App\Providers;

use App\ChangeloggerConfig;
use App\ChangesDirectory;
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
        // Boot here application
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChangeloggerConfig::class, static function () {
            return new ChangeloggerConfig(config('changelogger.directory'));
        });
        $this->app->singleton(ChangesDirectory::class, static function () {
            return new ChangesDirectory(config('changelogger.unreleased'));
        });
    }
}
