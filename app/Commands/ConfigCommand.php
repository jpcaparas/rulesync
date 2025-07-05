<?php

namespace JPCaparas\Rulesync\Commands;

use JPCaparas\Rulesync\Services\ConfigService;
use LaravelZero\Framework\Commands\Command;

class ConfigCommand extends Command
{
    protected $signature = 'config';

    protected $description = 'Display current configuration';

    public function handle(ConfigService $configService): int
    {
        $config = $configService->getConfig();
        $isLocal = $configService->isLocalProject();

        $this->line('<info>Rulesync Configuration</info>');
        $this->line('');
        $this->line('<comment>Location:</comment> '.($isLocal ? 'Local project' : 'Global'));
        $this->line('<comment>Config file:</comment> '.$configService->getConfigPath());
        $this->line('');

        if (empty($config)) {
            $this->line('<fg=yellow>No configuration found. Run commands to set up your configuration.</fg=yellow>');

            return self::SUCCESS;
        }

        if (isset($config['disabled_rules']) && ! empty($config['disabled_rules'])) {
            $this->line('<comment>Disabled rules:</comment>');
            foreach ($config['disabled_rules'] as $rule) {
                $this->line('  - '.$rule);
            }
        } else {
            $this->line('<comment>Disabled rules:</comment> <fg=green>None</fg=green>');
        }

        $this->line('');

        if (isset($config['base_rules_path']) && $config['base_rules_path']) {
            $this->line('<comment>Base rules:</comment> '.$config['base_rules_path']);
        } else {
            $this->line('<comment>Base rules:</comment> <fg=yellow>Not set</fg=yellow>');
        }

        $this->line('');

        if (isset($config['augment'])) {
            $augmentStatus = $config['augment'] ? '<fg=green>Yes</fg=green>' : '<fg=red>No</fg=red>';
            $this->line('<comment>Augment local + global rules:</comment> '.$augmentStatus);
        } else {
            $this->line('<comment>Augment local + global rules:</comment> <fg=yellow>Not set (will prompt)</fg=yellow>');
        }

        return self::SUCCESS;
    }
}
