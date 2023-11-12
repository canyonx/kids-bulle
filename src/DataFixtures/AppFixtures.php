<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $usersFixtures;

    public function __construct(
        UserFixtures $userFixtures
    ) {
        $this->usersFixtures = $userFixtures;
    }


    public function load(ObjectManager $manager): void
    {
        $this->usersFixtures->createUser();
    }
}
