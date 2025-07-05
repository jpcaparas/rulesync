<?php

namespace JPCaparas\Rulesync\Commands;

use JPCaparas\Rulesync\Services\ConfigService;
use JPCaparas\Rulesync\Services\RuleDiscoveryService;
use LaravelZero\Framework\Commands\Command;

class DisableCommand extends Command
{
    protected $signature = 'disable {name : The shortcode of the rule to disable}';

    protected $description = 'Disable a specific rule';

    public function handle(ConfigService $configService, RuleDiscoveryService $ruleDiscovery): int
    {
        $shortcode = $this->argument('name');
        $rule = $ruleDiscovery->getRuleByShortcode($shortcode);

        if (! $rule) {
            $this->error("Rule '{$shortcode}' not found.");

            return self::FAILURE;
        }

        $disabledRules = $configService->getDisabledRules();

        if (in_array($shortcode, $disabledRules)) {
            $this->line("<fg=yellow>Rule '{$shortcode}' is already disabled.</fg=yellow>");

            return self::SUCCESS;
        }

        $configService->disableRule($shortcode);
        $this->info("Rule '{$shortcode}' ({$rule->name()}) has been disabled.");

        return self::SUCCESS;
    }
}
