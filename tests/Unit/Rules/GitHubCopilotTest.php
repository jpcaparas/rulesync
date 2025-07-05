<?php

namespace Tests\Unit\Rules;

use JPCaparas\Rulesync\Rules\GitHubCopilot;
use JPCaparas\Rulesync\Rules\RuleInterface;
use Tests\TestCase;

class GitHubCopilotTest extends TestCase
{
    private GitHubCopilot $gitHubCopilot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gitHubCopilot = new GitHubCopilot;
    }

    public function test_implements_rule_interface(): void
    {
        expect($this->gitHubCopilot)->toBeInstanceOf(RuleInterface::class);
    }

    public function test_returns_correct_name(): void
    {
        expect($this->gitHubCopilot->name())->toBe('GitHub Copilot');
    }

    public function test_returns_correct_shortcode(): void
    {
        expect($this->gitHubCopilot->shortcode())->toBe('github-copilot');
    }

    public function test_returns_correct_path(): void
    {
        expect($this->gitHubCopilot->path())->toBe('.github/copilot-instructions.md');
    }

    public function test_name_is_not_empty(): void
    {
        expect($this->gitHubCopilot->name())->not->toBeEmpty();
    }

    public function test_shortcode_is_not_empty(): void
    {
        expect($this->gitHubCopilot->shortcode())->not->toBeEmpty();
    }

    public function test_path_is_not_empty(): void
    {
        expect($this->gitHubCopilot->path())->not->toBeEmpty();
    }

    public function test_name_is_string(): void
    {
        expect($this->gitHubCopilot->name())->toBeString();
    }

    public function test_shortcode_is_string(): void
    {
        expect($this->gitHubCopilot->shortcode())->toBeString();
    }

    public function test_path_is_string(): void
    {
        expect($this->gitHubCopilot->path())->toBeString();
    }
}
