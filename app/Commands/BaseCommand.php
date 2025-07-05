<?php

namespace JPCaparas\Rulesync\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use JPCaparas\Rulesync\Services\ConfigService;
use LaravelZero\Framework\Commands\Command;

class BaseCommand extends Command
{
    protected $signature = 'base {path? : URL or local file path} {--disable : Remove the base rules}';

    protected $description = 'Set or remove base rules file';

    public function handle(ConfigService $configService): int
    {
        if ($this->option('disable')) {
            return $this->disableBaseRules($configService);
        }

        $path = $this->argument('path');

        if (! $path) {
            $this->displayCurrentBaseRules($configService);

            return self::SUCCESS;
        }

        return $this->setBaseRules($configService, $path);
    }

    private function disableBaseRules(ConfigService $configService): int
    {
        $current = $configService->getBaseRulesPath();

        if (! $current) {
            $this->line('<fg=yellow>No base rules are currently set.</fg=yellow>');

            return self::SUCCESS;
        }

        $configService->setBaseRulesPath(null);
        $this->info('Base rules have been removed.');

        return self::SUCCESS;
    }

    private function displayCurrentBaseRules(ConfigService $configService): int
    {
        $current = $configService->getBaseRulesPath();

        if (! $current) {
            $this->line('<fg=yellow>No base rules are currently set.</fg=yellow>');
            $this->line('Use: <comment>rulesync base <path></comment> to set base rules');
        } else {
            $this->line('<info>Current base rules:</info> '.$current);
        }

        return self::SUCCESS;
    }

    private function setBaseRules(ConfigService $configService, string $path): int
    {
        if ($this->isUrl($path)) {
            return $this->setUrlBaseRules($configService, $path);
        }

        return $this->setLocalBaseRules($configService, $path);
    }

    private function setUrlBaseRules(ConfigService $configService, string $url): int
    {
        $this->line('Validating URL...');

        try {
            $response = Http::timeout(10)->get($url);

            if (! $response->successful()) {
                $this->error("Failed to access URL: {$url}");

                return self::FAILURE;
            }

            $content = $response->body();

            if (empty(trim($content))) {
                $this->error("URL returned empty content: {$url}");

                return self::FAILURE;
            }

            $configService->setBaseRulesPath($url);
            $this->info("Base rules set to URL: {$url}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to validate URL: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function setLocalBaseRules(ConfigService $configService, string $path): int
    {
        $fullPath = $this->resolveLocalPath($path);

        if (! File::exists($fullPath)) {
            $this->error("File does not exist: {$fullPath}");

            return self::FAILURE;
        }

        if (! File::isReadable($fullPath)) {
            $this->error("File is not readable: {$fullPath}");

            return self::FAILURE;
        }

        $configService->setBaseRulesPath($fullPath);
        $this->info("Base rules set to local file: {$fullPath}");

        return self::SUCCESS;
    }

    private function resolveLocalPath(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return $path;
        }

        return getcwd().'/'.$path;
    }

    private function isUrl(string $path): bool
    {
        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }
}
