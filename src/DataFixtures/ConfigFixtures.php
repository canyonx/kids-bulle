<?php

namespace App\DataFixtures;

use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;

class ConfigFixtures
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Create config
     *
     * @return void
     */
    public function createConfig(): void
    {
        $config = [
            'homepageTitle' => 'Welcome to our website',
            'homepageDescription' => 'This is the homepage of our website',
            'color' => '#000000',
            'code' => 'kb31'
        ];

        foreach ($config as $key => $value) {
            $config = new Config();
            $config->setName($key);
            $config->setValue($value);
            $this->entityManager->persist($config);
        }

        $this->entityManager->flush();
    }
}
