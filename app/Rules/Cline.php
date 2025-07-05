<?php

namespace JPCaparas\Rulesync\Rules;

class Cline implements RuleInterface
{
    public function name(): string
    {
        return 'Cline';
    }

    public function shortcode(): string
    {
        return 'cline';
    }

    public function path(): string
    {
        return '.clinerules/project.md';
    }

    public function gitignorePath(): string
    {
        return '.clinerules/';
    }
}
