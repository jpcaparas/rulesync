<?php

namespace JPCaparas\Rulesync\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use JPCaparas\Rulesync\Services\ConfigService;
use JPCaparas\Rulesync\Services\RuleDiscoveryService;
use LaravelZero\Framework\Commands\Command;

class GenerateCommand extends Command
{
    protected $signature = 'generate {--force : Force generation without prompts} {--overwrite : Overwrite existing files} {--from= : Custom source file path}';

    protected $description = 'Generate AI assistant rule files';

    public function handle(ConfigService $configService, RuleDiscoveryService $ruleDiscovery): int
    {
        if (! $this->checkVcsAndConfirm()) {
            return self::FAILURE;
        }

        $sourceFile = $this->getSourceFile($configService);

        if (! $sourceFile) {
            return self::FAILURE;
        }

        $sourceContent = $this->getSourceContent($sourceFile);

        if (! $sourceContent) {
            return self::FAILURE;
        }

        $baseRules = $this->getBaseRules($configService);
        $rules = $ruleDiscovery->getRules();
        $disabledRules = $configService->getDisabledRules();

        $enabledRules = array_filter($rules, function ($rule) use ($disabledRules) {
            return ! in_array($rule->shortcode(), $disabledRules);
        });

        if (empty($enabledRules)) {
            $this->line('<fg=yellow>No enabled rules found. Use "rulesync list" to see available rules.</fg=yellow>');

            return self::SUCCESS;
        }

        $this->line('<info>Generating rule files...</info>');
        $this->line('');

        $generated = 0;
        $skipped = 0;

        foreach ($enabledRules as $rule) {
            $targetPath = $rule->path();
            $targetDir = dirname($targetPath);

            if (! File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $finalContent = $this->buildFinalContent($sourceContent, $baseRules);

            if ($this->shouldWriteFile($targetPath, $finalContent)) {
                File::put($targetPath, $finalContent);
                $this->line("<fg=green>Generated:</fg=green> {$targetPath}");
                $generated++;
            } else {
                $this->line("<fg=yellow>Skipped:</fg=yellow> {$targetPath}");
                $skipped++;
            }
        }

        $this->line('');
        $this->info("Generation complete: {$generated} files generated, {$skipped} skipped.");

        return self::SUCCESS;
    }

    private function getSourceFile(ConfigService $configService): ?string
    {
        $customSource = $this->option('from');

        if ($customSource) {
            if (! File::exists($customSource)) {
                $this->error("Custom source file not found: {$customSource}");

                return null;
            }

            return $customSource;
        }

        $possibleSources = [
            getcwd().'/rulesync.md',
            $configService->getRulesDirectory().'/rulesync.md',
        ];

        foreach ($possibleSources as $source) {
            if (File::exists($source)) {
                return $source;
            }
        }

        $this->error('No source file found. Create a rulesync.md file or use --from option.');

        if ($configService->isLocalProject()) {
            $this->line('For local projects, create: <comment>rulesync.md</comment>');
        } else {
            $this->line('For global usage, create: <comment>~/.config/rulesync/rulesync.md</comment>');
        }

        return null;
    }

    private function getSourceContent(string $sourceFile): ?string
    {
        try {
            $content = File::get($sourceFile);

            if (empty(trim($content))) {
                $this->error("Source file is empty: {$sourceFile}");

                return null;
            }

            return $content;
        } catch (\Exception $e) {
            $this->error("Failed to read source file: {$e->getMessage()}");

            return null;
        }
    }

    private function getBaseRules(ConfigService $configService): ?string
    {
        $baseRulesPath = $configService->getBaseRulesPath();

        if (! $baseRulesPath) {
            return null;
        }

        try {
            if ($this->isUrl($baseRulesPath)) {
                $response = Http::timeout(10)->get($baseRulesPath);

                if (! $response->successful()) {
                    $this->warn("Failed to fetch base rules from URL: {$baseRulesPath}");

                    return null;
                }

                return $response->body();
            } else {
                if (! File::exists($baseRulesPath)) {
                    $this->warn("Base rules file not found: {$baseRulesPath}");

                    return null;
                }

                return File::get($baseRulesPath);
            }
        } catch (\Exception $e) {
            $this->warn("Failed to load base rules: {$e->getMessage()}");

            return null;
        }
    }

    private function buildFinalContent(string $sourceContent, ?string $baseRules): string
    {
        $content = $sourceContent;

        if ($baseRules && ! empty(trim($baseRules))) {
            $content .= "\n\n".$baseRules;
        }

        return $content;
    }

    private function shouldWriteFile(string $targetPath, string $content): bool
    {
        if (! File::exists($targetPath)) {
            return true;
        }

        if ($this->option('overwrite')) {
            return true;
        }

        if ($this->option('force')) {
            return true;
        }

        $existingContent = File::get($targetPath);
        $existingHash = md5($existingContent);
        $newHash = md5($content);

        if ($existingHash === $newHash) {
            return true;
        }

        return $this->confirm("File exists and is different: {$targetPath}. Overwrite?");
    }

    private function isUrl(string $path): bool
    {
        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }

    private function checkVcsAndConfirm(): bool
    {
        if ($this->option('force')) {
            return true;
        }

        if ($this->isUnderVersionControl()) {
            return true;
        }

        $this->warn('Warning: You are not in a version-controlled directory.');
        $this->line('Generating rule files without version control may result in lost changes.');
        $this->line('Consider initializing git with: <comment>git init</comment>');
        $this->line('');

        return $this->confirm('Do you want to continue anyway?');
    }

    private function isUnderVersionControl(): bool
    {
        return File::exists(getcwd() . '/.git');
    }
}
