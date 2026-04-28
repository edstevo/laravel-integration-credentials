<?php

namespace EdStevo\LaravelIntegrationCredentials\Tests;

use EdStevo\LaravelIntegrationCredentials\LaravelIntegrationCredentialsServiceProvider;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelIntegrationCredentialsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        foreach (File::allFiles(__DIR__.'/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }

        foreach (File::allFiles(__DIR__.'/../workbench/database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }
}
