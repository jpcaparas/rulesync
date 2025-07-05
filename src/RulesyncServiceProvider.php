<?php

namespace JPCaparas\Rulesync;

use JPCaparas\Rulesync\Commands\RulesyncCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
