<?php

namespace Tests\Feature;

use Tests\TestCase;

class EnableDisableCommandTest extends TestCase
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

    public function test_can_disable_rule(): void
    {
        $this->artisan('disable claude')
            ->expectsOutputToContain('has been disabled')
            ->assertExitCode(0);
    }

    public function test_can_enable_rule(): void
    {
        $this->artisan('enable claude')
            ->expectsOutputToContain('enabled')
            ->assertExitCode(0);
    }

    public function test_disable_nonexistent_rule_fails(): void
    {
        $this->artisan('disable nonexistent')
            ->expectsOutputToContain("Rule 'nonexistent' not found.")
            ->assertExitCode(1);
    }

    public function test_enable_nonexistent_rule_fails(): void
    {
        $this->artisan('enable nonexistent')
            ->expectsOutputToContain("Rule 'nonexistent' not found.")
            ->assertExitCode(1);
    }
}
