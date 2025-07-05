<?php

namespace JPCaparas\Rulesync\Rules;

interface RuleInterface
{
    public function name(): string;

    public function shortcode(): string;

    public function path(): string;
}
