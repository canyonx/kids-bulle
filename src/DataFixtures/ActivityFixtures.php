<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Category;
use App\Repository\ChildRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActivityFixtures
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChildRepository $childRepository
    ) {}

    /**
     * Create activities
     *
     * @return void
     */
    public function createActivities(): void
    {
        $children = $this->childRepository->findAll();

        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < 4; $i++) {
            $activity = new Activity;
            $activity->setCategory($categories[mt_rand(0, count($categories) - 1)])
                ->setDateAt(new \DateTimeImmutable('+3 days'));

            // Ajouter 6 enfants au hasard à l'activité
            $randomChildren = (array) array_rand($children, min(6, count($children)));
            foreach ($randomChildren as $childIndex) {
                $activity->addChildren($children[$childIndex]);
            }


            $this->entityManager->persist($activity);
        }
        $this->entityManager->flush();
    }
}
