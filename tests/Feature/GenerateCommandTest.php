<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GenerateCommandTest extends TestCase
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

    public function test_fails_when_no_source_file(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('generate')
            ->expectsOutput('No rulesync.md file found globally or locally.')
            ->expectsQuestion('Do you want to hide this warning in the future?', false)
            ->expectsOutputToContain('No source file found.')
            ->assertExitCode(1);
    }

    public function test_warns_when_not_in_version_control(): void
    {
        File::put($this->tempDir.'/rulesync.md', '# My Rules');

        $this->artisan('generate')
            ->expectsOutputToContain('Warning: You are not in a version-controlled directory.')
            ->expectsQuestion('Do you want to continue anyway?', false)
            ->assertExitCode(1);
    }

    public function test_continues_when_not_in_version_control_and_confirmed(): void
    {
        File::put($this->tempDir.'/rulesync.md', '# My Rules');

        $this->artisan('generate')
            ->expectsOutputToContain('Warning: You are not in a version-controlled directory.')
            ->expectsQuestion('Do you want to continue anyway?', true)
            ->expectsOutputToContain('Generated: CLAUDE.md')
            ->assertExitCode(0);
    }

    public function test_fails_when_custom_source_file_not_found(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('generate --from=/nonexistent.md --force')
            ->expectsOutputToContain('Custom source file not found: /nonexistent.md')
            ->assertExitCode(1);
    }

    public function test_handles_empty_source_file(): void
    {
        File::put($this->tempDir.'/rulesync.md', '');
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('generate --force')
            ->expectsOutputToContain('Source file is empty:')
            ->assertExitCode(1);
    }

    public function test_handles_whitespace_only_source_file(): void
    {
        File::put($this->tempDir.'/rulesync.md', "   \n\t  \n  ");
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('generate --force')
            ->expectsOutputToContain('Source file is empty:')
            ->assertExitCode(1);
    }

    public function test_basic_generation_works(): void
    {
        File::put($this->tempDir.'/rulesync.md', '# My Rules');
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        $this->artisan('generate --force')
            ->expectsOutputToContain('Generating rule files...')
            ->expectsOutputToContain('Generation complete:')
            ->assertExitCode(0);
    }

    public function test_hides_missing_global_rules_warning_when_confirmed(): void
    {
        File::put($this->tempDir.'/rulesync.md', '# My Rules');
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        // First run: expect warning and confirm to hide it
        $this->artisan('generate')
            ->expectsOutput('No global rulesync.md file found at ~/.config/rulesync/rulesync.md')
            ->expectsQuestion('Do you want to hide this warning in the future?', true)
            ->assertExitCode(0);

        // Second run: expect no warning
        $this->artisan('generate')
            ->doesntExpectOutput('No global rulesync.md file found at ~/.config/rulesync/rulesync.md')
            ->assertExitCode(0);
    }

    public function test_hides_missing_local_rules_warning_when_confirmed(): void
    {
        $globalConfigDir = $this->tempDir.'/.config/rulesync';
        File::ensureDirectoryExists($globalConfigDir);
        File::put($globalConfigDir.'/rulesync.md', '# Global Rules');
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        // First run: expect warning and confirm to hide it
        $this->artisan('generate')
            ->expectsOutput('No local rulesync.md file found in the current directory.')
            ->expectsQuestion('Do you want to hide this warning in the future?', true)
            ->assertExitCode(0);

        // Second run: expect no warning
        $this->artisan('generate')
            ->doesntExpectOutput('No local rulesync.md file found in the current directory.')
            ->assertExitCode(0);
    }

    public function test_hides_missing_rules_warning_when_confirmed(): void
    {
        File::ensureDirectoryExists($this->tempDir.'/.git');
        File::put($this->tempDir.'/.git/config', '');

        // First run: expect warning and confirm to hide it
        $this->artisan('generate')
            ->expectsOutput('No rulesync.md file found globally or locally.')
            ->expectsQuestion('Do you want to hide this warning in the future?', true)
            ->assertExitCode(1);

        // Second run: expect no warning
        $this->artisan('generate')
            ->doesntExpectOutput('No rulesync.md file found globally or locally.')
            ->assertExitCode(1);
    }
}
