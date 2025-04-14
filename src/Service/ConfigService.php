<?php

namespace App\Service;

use App\Repository\ConfigRepository;

class ConfigService
{

    public function __construct(
        private ConfigRepository $configRepository,
    ) {}

    public function get(string $name): string|null
    {
        $config = $this->configRepository->findOneBy(['name' => $name]);

        if ($config) {
            return $config->getValue();
        }

        return null;
    }

    public function getAll(): array
    {
        $configs = $this->configRepository->findAll();
        $configArray = [];

        foreach ($configs as $config) {
            $configArray[$config->getName()] = $config->getValue();
        }

        return $configArray;
    }
}
