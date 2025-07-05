<?php

namespace Tests\Unit\Services;

use JPCaparas\Rulesync\Rules\Claude;
use JPCaparas\Rulesync\Rules\Cursor;
use JPCaparas\Rulesync\Rules\RuleInterface;
use JPCaparas\Rulesync\Services\RuleDiscoveryService;
use Tests\TestCase;

class RuleDiscoveryServiceTest extends TestCase
{
    private RuleDiscoveryService $ruleDiscovery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruleDiscovery = new RuleDiscoveryService;
    }

    public function test_discovers_all_rule_classes(): void
    {
        $rules = $this->ruleDiscovery->getRules();

        expect($rules)->not->toBeEmpty();

        foreach ($rules as $rule) {
            expect($rule)->toBeInstanceOf(RuleInterface::class);
        }
    }

    public function test_excludes_rule_interface_from_discovery(): void
    {
        $rules = $this->ruleDiscovery->getRules();

        foreach ($rules as $rule) {
            expect(get_class($rule))->not->toBe(RuleInterface::class);
        }
    }

    public function test_discovers_claude_rule(): void
    {
        $rules = $this->ruleDiscovery->getRules();

        $claudeRule = collect($rules)->first(function ($rule) {
            return $rule instanceof Claude;
        });

        expect($claudeRule)->not->toBeNull();
        expect($claudeRule->name())->toBe('Claude');
        expect($claudeRule->shortcode())->toBe('claude');
        expect($claudeRule->path())->toBe('CLAUDE.md');
    }

    public function test_discovers_cursor_rule(): void
    {
        $rules = $this->ruleDiscovery->getRules();

        $cursorRule = collect($rules)->first(function ($rule) {
            return $rule instanceof Cursor;
        });

        expect($cursorRule)->not->toBeNull();
        expect($cursorRule->name())->toBe('Cursor');
        expect($cursorRule->shortcode())->toBe('cursor');
        expect($cursorRule->path())->toBe('.cursorrules');
    }

    public function test_can_find_rule_by_shortcode(): void
    {
        $claudeRule = $this->ruleDiscovery->getRuleByShortcode('claude');

        expect($claudeRule)->not->toBeNull();
        expect($claudeRule)->toBeInstanceOf(Claude::class);
        expect($claudeRule->shortcode())->toBe('claude');
    }

    public function test_returns_null_for_non_existent_shortcode(): void
    {
        $nonExistentRule = $this->ruleDiscovery->getRuleByShortcode('nonexistent');

        expect($nonExistentRule)->toBeNull();
    }

    public function test_handles_missing_rules_directory(): void
    {
        $tempDir = sys_get_temp_dir().'/rulesync_test_'.uniqid();
        mkdir($tempDir, 0755, true);

        $originalCwd = getcwd();
        chdir($tempDir);

        try {
            $service = new RuleDiscoveryService;
            $rules = $service->getRules();

            expect($rules)->not->toBeEmpty();
        } finally {
            chdir($originalCwd);
            exec("rm -rf {$tempDir}");
        }
    }

    public function test_all_discovered_rules_have_required_methods(): void
    {
        $rules = $this->ruleDiscovery->getRules();

        foreach ($rules as $rule) {
            expect($rule->name())->toBeString();
            expect($rule->shortcode())->toBeString();
            expect($rule->path())->toBeString();

            expect($rule->name())->not->toBeEmpty();
            expect($rule->shortcode())->not->toBeEmpty();
            expect($rule->path())->not->toBeEmpty();
        }
    }

    public function test_shortcodes_are_unique(): void
    {
        $rules = $this->ruleDiscovery->getRules();
        $shortcodes = collect($rules)->map(fn ($rule) => $rule->shortcode())->toArray();

        expect(array_unique($shortcodes))->toBe($shortcodes);
    }

    public function test_returns_multiple_rules(): void
    {
        $rules = $this->ruleDiscovery->getRules();

        expect(count($rules))->toBeGreaterThan(1);
    }

    public function test_all_rules_implement_interface(): void
    {
        $rules = $this->ruleDiscovery->getRules();

        foreach ($rules as $rule) {
            expect($rule)->toBeInstanceOf(RuleInterface::class);
        }
    }
}
