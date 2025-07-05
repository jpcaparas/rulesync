<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ListCommandTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir().'/rulesync_test_'.uniqid();
        mkdir($this->tempDir, 0755, true);

        putenv('HOME='.$this->tempDir);
        $_SERVER['HOME'] = $this->tempDir;
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            exec("rm -rf {$this->tempDir}");
        }

        parent::tearDown();
    }

    public function test_lists_all_available_rules(): void
    {
        $this->artisan('rules:list')
            ->expectsOutputToContain('Available Rules')
            ->expectsOutputToContain('Claude (claude)')
            ->expectsOutputToContain('CLAUDE.md')
            ->expectsOutputToContain('Cursor (cursor)')
            ->expectsOutputToContain('.cursorrules')
            ->expectsOutputToContain('enabled')
            ->assertExitCode(0);
    }

    public function test_shows_disabled_rules(): void
    {
        $configPath = $this->tempDir.'/.config/rulesync/rulesync.json';
        File::ensureDirectoryExists(dirname($configPath));
        File::put($configPath, json_encode([
            'disabled_rules' => ['claude'],
        ]));

        $this->artisan('rules:list')
            ->expectsOutputToContain('Claude')
            ->expectsOutputToContain('enabled')
            ->assertExitCode(0);
    }

    public function test_shows_enabled_rules(): void
    {
        $configPath = $this->tempDir.'/.config/rulesync/rulesync.json';
        File::ensureDirectoryExists(dirname($configPath));
        File::put($configPath, json_encode([
            'disabled_rules' => ['cursor'],
        ]));

        $this->artisan('rules:list')
            ->expectsOutputToContain('Claude')
            ->expectsOutputToContain('enabled')
            ->assertExitCode(0);
    }

    public function test_shows_all_expected_rules(): void
    {
        $this->artisan('rules:list')
            ->expectsOutputToContain('Claude')
            ->expectsOutputToContain('Cursor')
            ->expectsOutputToContain('Gemini CLI')
            ->expectsOutputToContain('GitHub Copilot')
            ->expectsOutputToContain('Windsurf')
            ->expectsOutputToContain('Junie')
            ->assertExitCode(0);
    }

    public function test_shows_correct_shortcodes(): void
    {
        $this->artisan('rules:list')
            ->expectsOutputToContain('Claude (claude)')
            ->expectsOutputToContain('Cursor (cursor)')
            ->expectsOutputToContain('Gemini CLI (gemini)')
            ->expectsOutputToContain('GitHub Copilot (github-copilot)')
            ->expectsOutputToContain('Windsurf (windsurf)')
            ->expectsOutputToContain('Junie (junie)')
            ->assertExitCode(0);
    }

    public function test_shows_correct_paths(): void
    {
        $this->artisan('rules:list')
            ->expectsOutputToContain('CLAUDE.md')
            ->expectsOutputToContain('.cursorrules')
            ->expectsOutputToContain('GEMINI.md')
            ->expectsOutputToContain('.github/copilot-instructions.md')
            ->expectsOutputToContain('.windsurfrules')
            ->expectsOutputToContain('.junie/guidelines.md')
            ->assertExitCode(0);
    }
}
