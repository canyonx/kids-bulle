<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryFixtures
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Create categories
     *
     * @return void
     */
    public function createCategories(): void
    {
        // create Categories
        $categories = [
            'Evenement',
            'Ski baby',
            'Ski sport',
            'Ski freeride',
            'Snow mini',
            'Snow sport',
            'Snow freeride',
        ];

        $colors = [
            '#30D078',
            '#62A1EB',
            '#3585E5',
            '#1A72D8',
            '#FFBD6D',
            '#FFA348',
            '#FF7800',
        ];

        foreach ($categories as $k => $category) {
            $cat = new Category;
            $cat->setName($category)
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus, congue vel laoreet ac, dictum vitae odio.')
                ->setColor($colors[$k])
                ->setNumber($k);
            $this->entityManager->persist($cat);
        }
        $this->entityManager->flush();
    }
}
