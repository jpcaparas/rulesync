<?php

namespace JPCaparas\Rulesync\Commands;

use Illuminate\Support\Facades\File;
use JPCaparas\Rulesync\Services\ConfigService;
use JPCaparas\Rulesync\Services\RuleDiscoveryService;
use LaravelZero\Framework\Commands\Command;

class GitignoreCommand extends Command
{
    protected $signature = 'gitignore:generate {--force : Force generation without prompts}';

    protected $description = 'Add AI assistant rule files to .gitignore';

    private const RULESYNC_START_MARKER = '# Begin powered by rulesync';

    private const RULESYNC_END_MARKER = '# End powered by rulesync - https://github.com/jpcaparas/rulesync';

    public function handle(ConfigService $configService, RuleDiscoveryService $ruleDiscovery): int
    {
        if (! $this->isGitRepository()) {
            $this->error('This command must be run in a git repository.');
            $this->line('Initialize git with: <comment>git init</comment>');

            return self::FAILURE;
        }

        $gitignorePath = getcwd().'/.gitignore';
        $rules = $ruleDiscovery->getRules();

        if (empty($rules)) {
            $this->line('<fg=yellow>No rules found.</fg=yellow>');

            return self::SUCCESS;
        }

        $gitignoreEntries = $this->collectGitignoreEntries($rules);

        if (empty($gitignoreEntries)) {
            $this->line('<fg=yellow>No gitignore entries to add.</fg=yellow>');

            return self::SUCCESS;
        }

        $shouldProceed = $this->shouldProceed($configService, $gitignorePath);

        if (! $shouldProceed) {
            $this->line('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->updateGitignoreFile($gitignorePath, $gitignoreEntries);

        $this->info('Successfully updated .gitignore with AI assistant rule files.');

        return self::SUCCESS;
    }

    private function isGitRepository(): bool
    {
        return File::exists(getcwd().'/.git');
    }

    private function collectGitignoreEntries(array $rules): array
    {
        $entries = [];

        foreach ($rules as $rule) {
            $gitignorePath = $rule->gitignorePath();
            if (! empty($gitignorePath)) {
                $entries[] = $gitignorePath;
            }
        }

        return array_unique($entries);
    }

    private function shouldProceed(ConfigService $configService, string $gitignorePath): bool
    {
        if ($this->option('force')) {
            return true;
        }

        if (! File::exists($gitignorePath)) {
            return $this->confirm('No .gitignore file found. Create one?');
        }

        $existingContent = File::get($gitignorePath);

        if ($this->hasRulesyncSection($existingContent)) {
            return true;
        }

        return $this->confirm('Modify existing .gitignore file?');
    }

    private function hasRulesyncSection(string $content): bool
    {
        return str_contains($content, self::RULESYNC_START_MARKER);
    }

    private function updateGitignoreFile(string $gitignorePath, array $entries): void
    {
        $existingContent = File::exists($gitignorePath) ? File::get($gitignorePath) : '';

        if ($this->hasRulesyncSection($existingContent)) {
            $newContent = $this->replaceRulesyncSection($existingContent, $entries);
        } else {
            $newContent = $this->appendRulesyncSection($existingContent, $entries);
        }

        File::put($gitignorePath, $newContent);
    }

    private function replaceRulesyncSection(string $content, array $entries): string
    {
        $startMarker = self::RULESYNC_START_MARKER;
        $endMarker = self::RULESYNC_END_MARKER;

        $pattern = '/^'.preg_quote($startMarker, '/').'.*?'.preg_quote($endMarker, '/').'$/ms';
        $replacement = $this->buildRulesyncSection($entries);

        return preg_replace($pattern, $replacement, $content);
    }

    private function appendRulesyncSection(string $content, array $entries): string
    {
        $content = rtrim($content);

        if (! empty($content)) {
            $content .= "\n\n";
        }

        $content .= $this->buildRulesyncSection($entries);

        return $content;
    }

    private function buildRulesyncSection(array $entries): string
    {
        $section = self::RULESYNC_START_MARKER."\n";

        foreach ($entries as $entry) {
            $section .= $entry."\n";
        }

        $section .= self::RULESYNC_END_MARKER;

        return $section;
    }
}
