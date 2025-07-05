<?php

namespace JPCaparas\Rulesync\Rules;

class Windsurf implements RuleInterface
{
    public function name(): string
    {
        return 'Windsurf';
    }

    public function shortcode(): string
    {
        return 'windsurf';
    }

    public function path(): string
    {
        return '.windsurfrules';
    }

    public function gitignorePath(): string
    {
        return '.windsurfrules';
    }
}
