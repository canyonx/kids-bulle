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
            // Accueil
            'homepageTitle' => 'Welcome to our website',
            'homepageDescription' => 'This is the homepage of our website',
            'columnNumber' => '3',
            // Couleurs
            'brandName' => 'Kid\'s Bulle',
            'color' => '#9a9996',
            'colorTheme' => '#3584e4',
            'logoFilename' => 'images/logo.png',
            // Sécurité
            'homepagePlanning' => 'true',
            'code' => 'kb31',
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
