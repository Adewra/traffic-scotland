<?php

namespace Adewra\TrafficScotland\Tests;
use Adewra\TrafficScotland\TrafficScotlandFacade;
use Adewra\TrafficScotland\TrafficScotlandServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return Illuminate\Support\ServiceProvider[]
     */
    protected function getPackageProviders($app)
    {
        return [TrafficScotlandServiceProvider::class];
    }
    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'TrafficScotland' => TrafficScotlandFacade::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
    }
}
