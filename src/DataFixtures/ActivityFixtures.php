<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class ActivityFixtures
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Create activities
     *
     * @return void
     */
    public function createActivities(): void
    {
        // create Activities

        $cat = $this->entityManager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < 4; $i++) {


            $activity = new Activity;
            $activity->setCategory($cat[mt_rand(0, count($cat) - 1)])
                ->setDateAt(new \DateTimeImmutable('+3 days'));
            $this->entityManager->persist($activity);
        }
        $this->entityManager->flush();
    }
}
