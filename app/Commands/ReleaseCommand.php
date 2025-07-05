<?php

namespace JPCaparas\Rulesync\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ReleaseCommand extends Command
{
    protected $signature = 'release {version : The version number (e.g., 1.0.0)}';

    protected $description = 'Build and prepare a release version';

    public function handle()
    {
        $version = $this->argument('version');

        if (! preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            $this->error('Invalid version format. Please use semantic versioning (e.g., 1.0.0)');

            return 1;
        }

        $this->info("Preparing release version {$version}...");

        $this->line('1. Running tests...');
        $testResult = $this->task('Running PHPUnit tests', function () {
            return exec('vendor/bin/pest 2>&1', $output, $returnVar) !== false && $returnVar === 0;
        });

        if (! $testResult) {
            $this->error('❌ Tests failed. Cannot proceed with release.');

            return 1;
        }

        $this->line('2. Building PHAR...');
        $buildResult = $this->call('build', ['version' => $version]);

        if ($buildResult !== 0) {
            $this->error('❌ Build failed. Cannot proceed with release.');

            return 1;
        }

        $this->info("✅ Release {$version} ready!");
        $this->line('📦 Executable: builds/rulesync');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Test the executable: ./builds/rulesync --version');
        $this->line('2. Create a Git tag: git tag v'.$version);
        $this->line('3. Push the tag: git push origin v'.$version);

        return 0;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
