<?php

namespace Yaranliu\Gadget;

use Illuminate\Support\ServiceProvider;
use Yaranliu\Gadget\Services\Gadget;

class GadgetServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'yaranliu');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'yaranliu');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gadget.php', 'gadget');

        // Register the service the package provides.
        $this->app->singleton('gadget', function ($app) {
            return new Gadget;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['gadget'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/gadget.php' => config_path('gadget.php'),
        ], 'gadget.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/yaranliu'),
        ], 'gadget.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/yaranliu'),
        ], 'gadget.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/yaranliu'),
        ], 'gadget.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
