<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BaseCommandTest extends TestCase
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

    public function test_shows_no_base_rules_when_not_set(): void
    {
        $this->artisan('base')
            ->expectsOutputToContain('base rules')
            ->assertExitCode(0);
    }

    public function test_can_set_local_base_rules(): void
    {
        $rulesFile = $this->tempDir.'/base-rules.md';
        File::put($rulesFile, '# Base Rules');

        $this->artisan("base {$rulesFile}")
            ->expectsOutputToContain("Base rules set to local file: {$rulesFile}")
            ->assertExitCode(0);
    }

    public function test_fails_when_local_file_not_found(): void
    {
        $nonExistentFile = $this->tempDir.'/nonexistent.md';

        $this->artisan("base {$nonExistentFile}")
            ->expectsOutputToContain("File does not exist: {$nonExistentFile}")
            ->assertExitCode(1);
    }

    public function test_handles_unreadable_file(): void
    {
        $rulesFile = $this->tempDir.'/unreadable.md';
        File::put($rulesFile, '# Base Rules');
        chmod($rulesFile, 0000);

        $this->artisan("base {$rulesFile}")
            ->expectsOutputToContain("File is not readable: {$rulesFile}")
            ->assertExitCode(1);

        chmod($rulesFile, 0644);
    }
}
