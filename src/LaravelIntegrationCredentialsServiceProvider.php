<?php

namespace EdStevo\LaravelIntegrationCredentials;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelIntegrationCredentialsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-integration-credentials')
            ->discoversMigrations()
            ->runsMigrations();
    }
}
