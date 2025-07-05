<?php

namespace JPCaparas\Rulesync\Rules;

class Cursor implements RuleInterface
{
    public function name(): string
    {
        return 'Cursor';
    }

    public function shortcode(): string
    {
        return 'cursor';
    }

    public function path(): string
    {
        return '.cursorrules';
    }

    public function gitignorePath(): string
    {
        return '.cursorrules';
    }
}
