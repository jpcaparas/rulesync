<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Facades\File;
use JPCaparas\Rulesync\Services\ConfigService;
use Tests\TestCase;

class ConfigServiceTest extends TestCase
{
    private ConfigService $configService;

    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir().'/rulesync_test_'.uniqid();
        mkdir($this->tempDir, 0755, true);

        putenv('HOME='.$this->tempDir);
        $_SERVER['HOME'] = $this->tempDir;

        chdir($this->tempDir);

        $this->configService = new ConfigService;
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            exec("rm -rf {$this->tempDir}");
        }

        parent::tearDown();
    }

    public function test_determines_local_project_when_composer_json_exists(): void
    {
        File::put($this->tempDir.'/composer.json', '{}');

        $configService = new ConfigService;

        expect($configService->isLocalProject())->toBeTrue();
    }

    public function test_determines_not_local_project_when_composer_json_missing(): void
    {
        expect($this->configService->isLocalProject())->toBeFalse();
    }

    public function test_returns_local_config_path_for_local_project(): void
    {
        File::put($this->tempDir.'/composer.json', '{}');

        $configService = new ConfigService;
        $expectedPath = getcwd().'/.rulesync/rulesync.json';

        expect($configService->getConfigPath())->toBe($expectedPath);
    }

    public function test_returns_global_config_path_for_non_local_project(): void
    {
        $expectedPath = $this->tempDir.'/.config/rulesync/rulesync.json';

        expect($this->configService->getConfigPath())->toBe($expectedPath);
    }

    public function test_creates_config_directory_if_not_exists(): void
    {
        $configDir = $this->configService->getConfigDirectory();

        expect(File::exists($configDir))->toBeTrue();
        expect(File::isDirectory($configDir))->toBeTrue();
    }

    public function test_returns_empty_disabled_rules_when_no_config(): void
    {
        expect($this->configService->getDisabledRules())->toBe([]);
    }

    public function test_returns_null_base_rules_path_when_no_config(): void
    {
        expect($this->configService->getBaseRulesPath())->toBeNull();
    }

    public function test_can_disable_rule(): void
    {
        $this->configService->disableRule('claude');

        expect($this->configService->getDisabledRules())->toContain('claude');
    }

    public function test_does_not_duplicate_disabled_rules(): void
    {
        $this->configService->disableRule('claude');
        $this->configService->disableRule('claude');

        expect($this->configService->getDisabledRules())->toBe(['claude']);
    }

    public function test_can_enable_rule(): void
    {
        $this->configService->disableRule('claude');
        $this->configService->enableRule('claude');

        expect($this->configService->getDisabledRules())->not->toContain('claude');
    }

    public function test_can_set_base_rules_path(): void
    {
        $path = '/path/to/base/rules.md';

        $this->configService->setBaseRulesPath($path);

        expect($this->configService->getBaseRulesPath())->toBe($path);
    }

    public function test_can_set_null_base_rules_path(): void
    {
        $this->configService->setBaseRulesPath('/some/path');
        $this->configService->setBaseRulesPath(null);

        expect($this->configService->getBaseRulesPath())->toBeNull();
    }

    public function test_can_set_augment_preference(): void
    {
        $this->configService->setAugmentPreference(true);

        expect($this->configService->getAugmentPreference())->toBeTrue();

        $this->configService->setAugmentPreference(false);

        expect($this->configService->getAugmentPreference())->toBeFalse();
    }

    public function test_returns_null_augment_preference_when_not_set(): void
    {
        expect($this->configService->getAugmentPreference())->toBeNull();
    }

    public function test_persists_config_to_file(): void
    {
        $this->configService->disableRule('claude');
        $this->configService->setBaseRulesPath('/test/path');

        $configPath = $this->configService->getConfigPath();

        expect(File::exists($configPath))->toBeTrue();

        $savedConfig = json_decode(File::get($configPath), true);

        expect($savedConfig['disabled_rules'])->toContain('claude');
        expect($savedConfig['base_rules_path'])->toBe('/test/path');
    }

    public function test_loads_existing_config_from_file(): void
    {
        $configPath = $this->configService->getConfigPath();
        $config = [
            'disabled_rules' => ['cursor', 'gemini'],
            'base_rules_path' => '/existing/path',
        ];

        File::put($configPath, json_encode($config));

        $newConfigService = new ConfigService;

        expect($newConfigService->getDisabledRules())->toBe(['cursor', 'gemini']);
        expect($newConfigService->getBaseRulesPath())->toBe('/existing/path');
    }

    public function test_handles_corrupted_config_file(): void
    {
        $configPath = $this->configService->getConfigPath();

        File::put($configPath, 'invalid json');

        $newConfigService = new ConfigService;

        expect($newConfigService->getDisabledRules())->toBe([]);
        expect($newConfigService->getBaseRulesPath())->toBeNull();
    }

    public function test_returns_rules_directory(): void
    {
        $expected = dirname($this->configService->getConfigPath());

        expect($this->configService->getRulesDirectory())->toBe($expected);
    }

    public function test_returns_full_config_array(): void
    {
        $this->configService->disableRule('claude');
        $this->configService->setBaseRulesPath('/test/path');
        $this->configService->setAugmentPreference(true);

        $config = $this->configService->getConfig();

        expect($config)->toHaveKey('disabled_rules');
        expect($config)->toHaveKey('base_rules_path');
        expect($config)->toHaveKey('augment');
        expect($config['disabled_rules'])->toContain('claude');
        expect($config['base_rules_path'])->toBe('/test/path');
        expect($config['augment'])->toBeTrue();
    }
}
