<?php

namespace Tests\Unit\Rules;

use JPCaparas\Rulesync\Rules\Junie;
use JPCaparas\Rulesync\Rules\RuleInterface;
use Tests\TestCase;

class JunieTest extends TestCase
{
    private Junie $junie;

    protected function setUp(): void
    {
        parent::setUp();

        $this->junie = new Junie;
    }

    public function test_implements_rule_interface(): void
    {
        expect($this->junie)->toBeInstanceOf(RuleInterface::class);
    }

    public function test_returns_correct_name(): void
    {
        expect($this->junie->name())->toBe('Junie');
    }

    public function test_returns_correct_shortcode(): void
    {
        expect($this->junie->shortcode())->toBe('junie');
    }

    public function test_returns_correct_path(): void
    {
        expect($this->junie->path())->toBe('.junie/guidelines.md');
    }

    public function test_name_is_not_empty(): void
    {
        expect($this->junie->name())->not->toBeEmpty();
    }

    public function test_shortcode_is_not_empty(): void
    {
        expect($this->junie->shortcode())->not->toBeEmpty();
    }

    public function test_path_is_not_empty(): void
    {
        expect($this->junie->path())->not->toBeEmpty();
    }

    public function test_name_is_string(): void
    {
        expect($this->junie->name())->toBeString();
    }

    public function test_shortcode_is_string(): void
    {
        expect($this->junie->shortcode())->toBeString();
    }

    public function test_path_is_string(): void
    {
        expect($this->junie->path())->toBeString();
    }
}
