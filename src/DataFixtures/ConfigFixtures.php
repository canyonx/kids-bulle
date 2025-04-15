<?php

namespace App\DataFixtures;

use App\Entity\Config;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConfigFixtures
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
    ) {}

    /**
     * Create config
     *
     * @return void
     */
    public function createConfig(): void
    {
        // Load the configuration fields from the YAML file /config/database_config_fields.yaml
        $config = Yaml::parseFile($this->parameterBag->get('kernel.project_dir') . '/config/database_config_fields.yaml')['fields'];

        foreach ($config as $key => $value) {
            $config = new Config();
            $config->setName($key);
            $config->setValue($value);
            $this->entityManager->persist($config);
        }

        $this->entityManager->flush();
    }
}
