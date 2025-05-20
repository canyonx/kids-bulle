<?php

namespace App\Service;

use App\Repository\ConfigRepository;

class ConfigService
{
    private ?array $config = null;

    public function __construct(
        private ConfigRepository $configRepository,
    ) {}

    private function load(): void
    {
        if ($this->config === null) {
            $this->config = [];
            foreach ($this->configRepository->findAll() as $setting) {
                $this->config[$setting->getName()] = $setting->getValue();
            }
        }
    }

    public function get(string $name, mixed $default = null): mixed
    {
        $this->load();
        return $this->config[$name] ?? $default;
    }

    public function all(): array
    {
        $this->load();
        return $this->config;
    }
}
