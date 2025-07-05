<?php

namespace JPCaparas\Rulesync\Rules;

class Junie implements RuleInterface
{
    public function name(): string
    {
        return 'Junie';
    }

    public function shortcode(): string
    {
        return 'junie';
    }

    public function path(): string
    {
        return '.junie/guidelines.md';
    }
}