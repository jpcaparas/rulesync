<?php

namespace JPCaparas\Rulesync;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use JPCaparas\Rulesync\Commands\RulesyncCommand;

class RulesyncServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('rulesync')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_rulesync_table')
            ->hasCommand(RulesyncCommand::class);
    }
}
