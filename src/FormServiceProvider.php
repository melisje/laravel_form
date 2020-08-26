<?php

namespace Melit\Form;

use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      $this->loadRoutesFrom(__DIR__.'/routes.php');
      $this->loadMigrationsFrom(__DIR__.'/migrations');
      $this->loadViewsFrom(__DIR__.'/views', 'utils');
      $this->publishes([
          __DIR__.'/views' => base_path('resources/views/melit/utils'),
      ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('melit\utils\SettingsController');
    }
}
