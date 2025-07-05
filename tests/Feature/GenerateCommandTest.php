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

        $this->artisan('generate --force')
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
}
