<?php

namespace JPCaparas\Rulesync\Services;

use Illuminate\Support\Facades\File;
use JPCaparas\Rulesync\Rules\RuleInterface;

class RuleDiscoveryService
{
    public function getRules(): array
    {
        $rules = [];
        $rulesPath = app_path('Rules');

        if (! File::exists($rulesPath)) {
            return $rules;
        }

        $files = File::files($rulesPath);

        foreach ($files as $file) {
            $className = $file->getBasename('.php');

            if ($className === 'RuleInterface') {
                continue;
            }

            $fullClassName = "JPCaparas\\Rulesync\\Rules\\{$className}";

            if (class_exists($fullClassName)) {
                $reflection = new \ReflectionClass($fullClassName);

                if ($reflection->implementsInterface(RuleInterface::class) && ! $reflection->isAbstract()) {
                    $rules[] = app($fullClassName);
                }
            }
        }

        return $rules;
    }

    public function getRuleByShortcode(string $shortcode): ?RuleInterface
    {
        $rules = $this->getRules();

        foreach ($rules as $rule) {
            if ($rule->shortcode() === $shortcode) {
                return $rule;
            }
        }

        return null;
    }
}
