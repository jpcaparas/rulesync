<?php

namespace JPCaparas\Rulesync\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class BuildCommand extends Command
{
    protected $signature = 'build {version? : Specify the build version}';

    protected $description = 'Build a standalone PHAR executable';

    public function handle()
    {
        $this->info('Building standalone PHAR executable...');

        $version = $this->argument('version');

        // Update version in config if specified
        if ($version) {
            $this->line("Setting version to {$version}...");
            $configPath = config_path('app.php');
            $configContent = file_get_contents($configPath);
            $configContent = preg_replace(
                "/'version' => '[^']*'/",
                "'version' => '{$version}'",
                $configContent
            );
            file_put_contents($configPath, $configContent);
        }

        $this->line('Creating builds directory...');
        if (! is_dir('builds')) {
            mkdir('builds', 0755, true);
        }

        $boxCommand = 'box compile';
        if ($this->option('verbose')) {
            $boxCommand .= ' -vvv';
        }

        $this->line('Running Box compile...');

        $output = [];
        $returnVar = 0;
        exec($boxCommand.' 2>&1', $output, $returnVar);

        if ($this->option('verbose')) {
            $this->line('Box output:');
            foreach ($output as $line) {
                $this->line($line);
            }
        }

        if ($returnVar === 0) {
            $this->info('✅ Build completed successfully!');
            $this->line('📦 Executable created at: builds/rulesync');
            $this->line('');
            $this->line('To test the build:');
            $this->line('  ./builds/rulesync --version');
        } else {
            $this->error('❌ Build failed. Box output:');
            foreach ($output as $line) {
                $this->line($line);
            }

            return 1;
        }

        return 0;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
