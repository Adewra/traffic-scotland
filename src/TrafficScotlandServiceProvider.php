<?php

namespace Adewra\TrafficScotland;

use Illuminate\Support\ServiceProvider;

class TrafficScotlandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/trafficscotland.php' => config_path('trafficscotland.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/Migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\CurrentIncidentsCommand::class,
                Console\RoadworksCommand::class
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /* Common Binding */
        $this->app->bind('adewra-trafficscotland', function () {
            return new Client;
        });
    }
}
