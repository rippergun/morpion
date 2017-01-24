<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MorpionProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(
            'App\Game\Storage\StorageInterface',
            'App\Game\Storage\Session'
        );

        $this->app->bind('App\Game\Storage\Session', function ($app) {
            return new \App\Game\Storage\Session($app->make('\Illuminate\Session\Store'));
        });

        $this->app->bind('App\Game\Logic', function ($app) {
            return new \App\Game\Logic($app->make('App\Game\Storage\Session'));
        });

    }
}
