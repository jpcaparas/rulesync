<?php

namespace Tests\Unit\Rules;

use JPCaparas\Rulesync\Rules\Cursor;
use JPCaparas\Rulesync\Rules\RuleInterface;
use Tests\TestCase;

class CursorTest extends TestCase
{
    private Cursor $cursor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cursor = new Cursor;
    }

    public function test_implements_rule_interface(): void
    {
        expect($this->cursor)->toBeInstanceOf(RuleInterface::class);
    }

    public function test_returns_correct_name(): void
    {
        expect($this->cursor->name())->toBe('Cursor');
    }

    public function test_returns_correct_shortcode(): void
    {
        expect($this->cursor->shortcode())->toBe('cursor');
    }

    public function test_returns_correct_path(): void
    {
        expect($this->cursor->path())->toBe('.cursorrules');
    }

    public function test_name_is_not_empty(): void
    {
        expect($this->cursor->name())->not->toBeEmpty();
    }

    public function test_shortcode_is_not_empty(): void
    {
        expect($this->cursor->shortcode())->not->toBeEmpty();
    }

    public function test_path_is_not_empty(): void
    {
        expect($this->cursor->path())->not->toBeEmpty();
    }

    public function test_name_is_string(): void
    {
        expect($this->cursor->name())->toBeString();
    }

    public function test_shortcode_is_string(): void
    {
        expect($this->cursor->shortcode())->toBeString();
    }

    public function test_path_is_string(): void
    {
        expect($this->cursor->path())->toBeString();
    }
}
