<?php

namespace EdStevo\LaravelIntegrationCredentials\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            \EdStevo\LaravelIntegrationCredentials\LaravelIntegrationCredentialsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__.'/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }

        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__.'/../workbench/database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }
}
