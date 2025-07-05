<?php

namespace JPCaparas\Rulesync\Rules;

class Gemini implements RuleInterface
{
    public function name(): string
    {
        return 'Gemini CLI';
    }

    public function shortcode(): string
    {
        return 'gemini';
    }

    public function path(): string
    {
        return 'GEMINI.md';
    }
}
