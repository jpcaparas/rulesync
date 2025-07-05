<?php

namespace JPCaparas\Rulesync\Rules;

class Claude implements RuleInterface
{
    public function name(): string
    {
        return 'Claude';
    }

    public function shortcode(): string
    {
        return 'claude';
    }

    public function path(): string
    {
        return 'CLAUDE.md';
    }

    public function gitignorePath(): string
    {
        return 'CLAUDE.md';
    }
}
