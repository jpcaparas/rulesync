<?php

namespace Tests\Unit\Rules;

use JPCaparas\Rulesync\Rules\Claude;
use JPCaparas\Rulesync\Rules\RuleInterface;
use Tests\TestCase;

class ClaudeTest extends TestCase
{
    private Claude $claude;

    protected function setUp(): void
    {
        parent::setUp();

        $this->claude = new Claude;
    }

    public function test_implements_rule_interface(): void
    {
        expect($this->claude)->toBeInstanceOf(RuleInterface::class);
    }

    public function test_returns_correct_name(): void
    {
        expect($this->claude->name())->toBe('Claude');
    }

    public function test_returns_correct_shortcode(): void
    {
        expect($this->claude->shortcode())->toBe('claude');
    }

    public function test_returns_correct_path(): void
    {
        expect($this->claude->path())->toBe('CLAUDE.md');
    }

    public function test_name_is_not_empty(): void
    {
        expect($this->claude->name())->not->toBeEmpty();
    }

    public function test_shortcode_is_not_empty(): void
    {
        expect($this->claude->shortcode())->not->toBeEmpty();
    }

    public function test_path_is_not_empty(): void
    {
        expect($this->claude->path())->not->toBeEmpty();
    }

    public function test_name_is_string(): void
    {
        expect($this->claude->name())->toBeString();
    }

    public function test_shortcode_is_string(): void
    {
        expect($this->claude->shortcode())->toBeString();
    }

    public function test_path_is_string(): void
    {
        expect($this->claude->path())->toBeString();
    }
}
