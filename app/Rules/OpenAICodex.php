<?php

namespace JPCaparas\Rulesync\Rules;

class OpenAICodex implements RuleInterface
{
    public function name(): string
    {
        return 'OpenAI Codex';
    }

    public function shortcode(): string
    {
        return 'codex';
    }

    public function path(): string
    {
        return 'AGENTS.md';
    }

    public function gitignorePath(): string
    {
        return 'AGENTS.md';
    }
}
