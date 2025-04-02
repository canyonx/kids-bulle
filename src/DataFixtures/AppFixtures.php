<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        private ConfigFixtures $configFixtures,
        private UserFixtures $userFixtures,
        private CategoryFixtures $categoryFixtures,
        private ActivityFixtures $activityFixtures,
    ) {}


    public function load(ObjectManager $manager): void
    {
        $this->configFixtures->createConfig();
        $this->userFixtures->createUsers();
        $this->categoryFixtures->createCategories();
        $this->activityFixtures->createActivities();
    }
}
