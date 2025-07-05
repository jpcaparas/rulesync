<?php

namespace JPCaparas\Rulesync\Services;

use Illuminate\Support\Facades\File;

class ConfigService
{
    private string $configPath;

    private array $config;

    public function __construct()
    {
        $this->configPath = $this->getConfigPath();
        $this->ensureConfigDirectoryExists();
        $this->loadConfig();
    }

    public function getConfigPath(): string
    {
        if ($this->isLocalProject()) {
            return getcwd().'/.rulesync/rulesync.json';
        }

        return $_SERVER['HOME'].'/.config/rulesync/rulesync.json';
    }

    public function getConfigDirectory(): string
    {
        return dirname($this->configPath);
    }

    public function getRulesDirectory(): string
    {
        return dirname($this->configPath);
    }

    public function isLocalProject(): bool
    {
        return File::exists(getcwd().'/composer.json');
    }

    public function getDisabledRules(): array
    {
        return $this->config['disabled_rules'] ?? [];
    }

    public function getBaseRulesPath(): ?string
    {
        return $this->config['base_rules_path'] ?? null;
    }

    public function disableRule(string $shortcode): void
    {
        $disabled = $this->getDisabledRules();
        if (! in_array($shortcode, $disabled)) {
            $disabled[] = $shortcode;
            $this->config['disabled_rules'] = $disabled;
            $this->saveConfig();
        }
    }

    public function enableRule(string $shortcode): void
    {
        $disabled = $this->getDisabledRules();
        $this->config['disabled_rules'] = array_values(array_filter($disabled, fn ($rule) => $rule !== $shortcode));
        $this->saveConfig();
    }

    public function setBaseRulesPath(?string $path): void
    {
        $this->config['base_rules_path'] = $path;
        $this->saveConfig();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getAugmentPreference(): ?bool
    {
        if (! isset($this->config['augment'])) {
            return null;
        }

        return $this->config['augment'];
    }

    public function setAugmentPreference(bool $augment): void
    {
        $this->config['augment'] = $augment;
        $this->saveConfig();
    }

    private function ensureConfigDirectoryExists(): void
    {
        $directory = dirname($this->configPath);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    private function loadConfig(): void
    {
        if (File::exists($this->configPath)) {
            $this->config = json_decode(File::get($this->configPath), true) ?? [];
        } else {
            $this->config = [];
        }
    }

    private function saveConfig(): void
    {
        File::put($this->configPath, json_encode($this->config, JSON_PRETTY_PRINT));
    }
}
