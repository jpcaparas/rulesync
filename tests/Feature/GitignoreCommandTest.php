<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GitignoreCommandTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir().'/rulesync_test_'.uniqid();
        mkdir($this->tempDir, 0755, true);

        putenv('HOME='.$this->tempDir);
        $_SERVER['HOME'] = $this->tempDir;

        chdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            exec("rm -rf {$this->tempDir}");
        }

        parent::tearDown();
    }

    public function test_fails_when_not_in_git_repository(): void
    {
        $this->artisan('gitignore:generate --force')
            ->expectsOutputToContain('This command must be run in a git repository.')
            ->assertExitCode(1);
    }

    public function test_creates_new_gitignore_file(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('gitignore:generate')
            ->expectsQuestion('No .gitignore file found. Create one?', true)
            ->expectsOutputToContain('Successfully updated .gitignore with AI assistant rule files.')
            ->assertExitCode(0);

        $gitignoreContent = File::get($this->tempDir.'/.gitignore');
        $this->assertStringContainsString('# Begin powered by rulesync', $gitignoreContent);
        $this->assertStringContainsString('# End powered by rulesync - https://github.com/jpcaparas/rulesync', $gitignoreContent);
        $this->assertStringContainsString('CLAUDE.md', $gitignoreContent);
        $this->assertStringContainsString('.cursorrules', $gitignoreContent);
    }

    public function test_modifies_existing_gitignore_file(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');
        File::put($this->tempDir.'/.gitignore', "# Existing content\n*.log\n");

        $this->artisan('gitignore:generate')
            ->expectsQuestion('Modify existing .gitignore file?', true)
            ->expectsOutputToContain('Successfully updated .gitignore with AI assistant rule files.')
            ->assertExitCode(0);

        $gitignoreContent = File::get($this->tempDir.'/.gitignore');
        $this->assertStringContainsString('# Existing content', $gitignoreContent);
        $this->assertStringContainsString('*.log', $gitignoreContent);
        $this->assertStringContainsString('# Begin powered by rulesync', $gitignoreContent);
        $this->assertStringContainsString('CLAUDE.md', $gitignoreContent);
    }

    public function test_replaces_existing_rulesync_section(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $existingGitignore = "# Existing content\n*.log\n\n# Begin powered by rulesync\nOLD_RULE.md\n# End powered by rulesync - https://github.com/jpcaparas/rulesync\n";
        File::put($this->tempDir.'/.gitignore', $existingGitignore);

        $this->artisan('gitignore:generate --force')
            ->expectsOutputToContain('Successfully updated .gitignore with AI assistant rule files.')
            ->assertExitCode(0);

        $gitignoreContent = File::get($this->tempDir.'/.gitignore');
        $this->assertStringContainsString('# Existing content', $gitignoreContent);
        $this->assertStringContainsString('*.log', $gitignoreContent);
        $this->assertStringContainsString('# Begin powered by rulesync', $gitignoreContent);
        $this->assertStringContainsString('CLAUDE.md', $gitignoreContent);
        $this->assertStringNotContainsString('OLD_RULE.md', $gitignoreContent);
    }

    public function test_declines_to_create_new_gitignore(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('gitignore:generate')
            ->expectsQuestion('No .gitignore file found. Create one?', false)
            ->expectsOutputToContain('Operation cancelled.')
            ->assertExitCode(0);

        $this->assertFalse(File::exists($this->tempDir.'/.gitignore'));
    }

    public function test_declines_to_modify_existing_gitignore(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');
        $originalContent = "# Existing content\n*.log\n";
        File::put($this->tempDir.'/.gitignore', $originalContent);

        $this->artisan('gitignore:generate')
            ->expectsQuestion('Modify existing .gitignore file?', false)
            ->expectsOutputToContain('Operation cancelled.')
            ->assertExitCode(0);

        $gitignoreContent = File::get($this->tempDir.'/.gitignore');
        $this->assertEquals($originalContent, $gitignoreContent);
    }

    public function test_force_flag_skips_prompts(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('gitignore:generate --force')
            ->expectsOutputToContain('Successfully updated .gitignore with AI assistant rule files.')
            ->assertExitCode(0);

        $gitignoreContent = File::get($this->tempDir.'/.gitignore');
        $this->assertStringContainsString('# Begin powered by rulesync', $gitignoreContent);
        $this->assertStringContainsString('CLAUDE.md', $gitignoreContent);
    }

    public function test_includes_all_rule_gitignore_paths(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('gitignore:generate --force')
            ->expectsOutputToContain('Successfully updated .gitignore with AI assistant rule files.')
            ->assertExitCode(0);

        $gitignoreContent = File::get($this->tempDir.'/.gitignore');

        $this->assertStringContainsString('CLAUDE.md', $gitignoreContent);
        $this->assertStringContainsString('.clinerules/', $gitignoreContent);
        $this->assertStringContainsString('.cursorrules', $gitignoreContent);
        $this->assertStringContainsString('GEMINI.md', $gitignoreContent);
        $this->assertStringContainsString('.github/copilot-instructions.md', $gitignoreContent);
        $this->assertStringContainsString('.junie/', $gitignoreContent);
        $this->assertStringContainsString('AGENTS.md', $gitignoreContent);
        $this->assertStringContainsString('.windsurfrules', $gitignoreContent);
    }
}
