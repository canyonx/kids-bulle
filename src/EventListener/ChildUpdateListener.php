<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Child;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChildUpdateListener
{
    private $em;
    private $activityRepository;

    public function __construct(
        EntityManagerInterface $em,
        ActivityRepository $activityRepository
    ) {
        $this->em = $em;
        $this->activityRepository = $activityRepository;
    }

    public function postUpdate(
        Child $child,
        PostUpdateEventArgs $event
    ): void {
        // Remove existing activities
        $activities = $this->activityRepository->findByChild($child);
        foreach ($activities as $activity) {
            $child->removeActivity($activity);
        }
        $this->em->flush();

        // Add new activities
        if ($child->getCategory()) {
            $activities = $this->activityRepository->findBy(['category' => $child->getCategory()]);
            foreach ($activities as $activity) {
                $child->addActivity($activity);
            }
        }

        $this->em->flush();
    }
}
