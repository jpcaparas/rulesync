<?php

namespace JPCaparas\Rulesync\Commands;

use JPCaparas\Rulesync\Services\ConfigService;
use JPCaparas\Rulesync\Services\RuleDiscoveryService;
use LaravelZero\Framework\Commands\Command;

class EnableCommand extends Command
{
    protected $signature = 'enable {name : The shortcode of the rule to enable}';

    protected $description = 'Enable a specific rule';

    public function handle(ConfigService $configService, RuleDiscoveryService $ruleDiscovery): int
    {
        $shortcode = $this->argument('name');
        $rule = $ruleDiscovery->getRuleByShortcode($shortcode);

        if (! $rule) {
            $this->error("Rule '{$shortcode}' not found.");

            return self::FAILURE;
        }

        $disabledRules = $configService->getDisabledRules();

        if (! in_array($shortcode, $disabledRules)) {
            $this->line("<fg=yellow>Rule '{$shortcode}' is already enabled.</fg=yellow>");

            return self::SUCCESS;
        }

        $configService->enableRule($shortcode);
        $this->info("Rule '{$shortcode}' ({$rule->name()}) has been enabled.");

        return self::SUCCESS;
    }
}
