<?php

namespace Dibs;

use Dibs\Auth\DibsUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class DibsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Auth::provider('dibs', function ($app, array $config) {
            return new DibsUserProvider;
        });
    }

    private function registerConfig()
    {
        $configPath = __DIR__ . '/../../config/dibs.php';
        $this->publishes([$configPath => config_path('dibs.php')], 'config');
        $this->mergeConfigFrom($configPath, 'dibs');
    }
}
