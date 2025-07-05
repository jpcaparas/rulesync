<?php

namespace Tests\Unit\Rules;

use JPCaparas\Rulesync\Rules\Gemini;
use JPCaparas\Rulesync\Rules\RuleInterface;
use Tests\TestCase;

class GeminiTest extends TestCase
{
    private Gemini $gemini;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gemini = new Gemini;
    }

    public function test_implements_rule_interface(): void
    {
        expect($this->gemini)->toBeInstanceOf(RuleInterface::class);
    }

    public function test_returns_correct_name(): void
    {
        expect($this->gemini->name())->toBe('Gemini CLI');
    }

    public function test_returns_correct_shortcode(): void
    {
        expect($this->gemini->shortcode())->toBe('gemini');
    }

    public function test_returns_correct_path(): void
    {
        expect($this->gemini->path())->toBe('GEMINI.md');
    }

    public function test_name_is_not_empty(): void
    {
        expect($this->gemini->name())->not->toBeEmpty();
    }

    public function test_shortcode_is_not_empty(): void
    {
        expect($this->gemini->shortcode())->not->toBeEmpty();
    }

    public function test_path_is_not_empty(): void
    {
        expect($this->gemini->path())->not->toBeEmpty();
    }

    public function test_name_is_string(): void
    {
        expect($this->gemini->name())->toBeString();
    }

    public function test_shortcode_is_string(): void
    {
        expect($this->gemini->shortcode())->toBeString();
    }

    public function test_path_is_string(): void
    {
        expect($this->gemini->path())->toBeString();
    }
}
