<?php

namespace JPCaparas\Rulesync\Rules;

class GitHubCopilot implements RuleInterface
{
    public function name(): string
    {
        return 'GitHub Copilot';
    }

    public function shortcode(): string
    {
        return 'github-copilot';
    }

    public function path(): string
    {
        return '.github/copilot-instructions.md';
    }

    public function gitignorePath(): string
    {
        return '.github/copilot-instructions.md';
    }
}
