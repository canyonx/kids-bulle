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

        // Générer des activités pour le mois précédent jusqu'aux 3 prochains mois
        $startDate = (new \DateTimeImmutable('first day of last month'))->setTime(0, 0);
        $endDate = (new \DateTimeImmutable('last day of +3 months'))->setTime(23, 59);

        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            for ($i = 0; $i < 4; $i++) { // Créer 4 activités par période
                $activity = new Activity;

                // Générer une heure et des minutes aléatoires
                $randomTime = $currentDate->setTime(mt_rand(8, 18), mt_rand(0, 59));

                $activity->setCategory($categories[mt_rand(0, count($categories) - 1)])
                    ->setDateAt($randomTime);

                // Ajouter 6 enfants au hasard à l'activité
                $randomChildren = (array) array_rand($children, min(mt_rand(6, 14), count($children)));
                foreach ($randomChildren as $childIndex) {
                    $activity->addChildren($children[$childIndex]);
                }

                $this->entityManager->persist($activity);
            }

            // Passer à la prochaine période (par exemple, ajouter 7 jours)
            $currentDate = $currentDate->modify('+7 days');
        }

        $this->entityManager->flush();
    }
}
