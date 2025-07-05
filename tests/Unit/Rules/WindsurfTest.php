<?php

namespace Tests\Unit\Rules;

use JPCaparas\Rulesync\Rules\RuleInterface;
use JPCaparas\Rulesync\Rules\Windsurf;
use Tests\TestCase;

class WindsurfTest extends TestCase
{
    private Windsurf $windsurf;

    protected function setUp(): void
    {
        parent::setUp();

        $this->windsurf = new Windsurf;
    }

    public function test_implements_rule_interface(): void
    {
        expect($this->windsurf)->toBeInstanceOf(RuleInterface::class);
    }

    public function test_returns_correct_name(): void
    {
        expect($this->windsurf->name())->toBe('Windsurf');
    }

    public function test_returns_correct_shortcode(): void
    {
        expect($this->windsurf->shortcode())->toBe('windsurf');
    }

    public function test_returns_correct_path(): void
    {
        expect($this->windsurf->path())->toBe('.windsurfrules');
    }

    public function test_name_is_not_empty(): void
    {
        expect($this->windsurf->name())->not->toBeEmpty();
    }

    public function test_shortcode_is_not_empty(): void
    {
        expect($this->windsurf->shortcode())->not->toBeEmpty();
    }

    public function test_path_is_not_empty(): void
    {
        expect($this->windsurf->path())->not->toBeEmpty();
    }

    public function test_name_is_string(): void
    {
        expect($this->windsurf->name())->toBeString();
    }

    public function test_shortcode_is_string(): void
    {
        expect($this->windsurf->shortcode())->toBeString();
    }

    public function test_path_is_string(): void
    {
        expect($this->windsurf->path())->toBeString();
    }
}
