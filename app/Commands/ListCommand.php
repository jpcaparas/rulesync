<?php

namespace JPCaparas\Rulesync\Commands;

use JPCaparas\Rulesync\Services\ConfigService;
use JPCaparas\Rulesync\Services\RuleDiscoveryService;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    protected $signature = 'rules:list';

    protected $description = 'List all available rules';

    public function handle(RuleDiscoveryService $ruleDiscovery, ConfigService $configService): int
    {
        $rules = $ruleDiscovery->getRules();
        $disabledRules = $configService->getDisabledRules();

        if (empty($rules)) {
            $this->line('<fg=yellow>No rules available.</fg=yellow>');

            return self::SUCCESS;
        }

        $this->line('<info>Available Rules</info>');
        $this->line('');

        foreach ($rules as $rule) {
            $shortcode = $rule->shortcode();
            $status = in_array($shortcode, $disabledRules) ? '<fg=red>disabled</fg=red>' : '<fg=green>enabled</fg=green>';

            $this->line(sprintf(
                '  <comment>%s</comment> (%s) - %s',
                $rule->name(),
                $shortcode,
                $status
            ));
            $this->line('    Path: '.$rule->path());
            $this->line('');
        }

        return self::SUCCESS;
    }
}
