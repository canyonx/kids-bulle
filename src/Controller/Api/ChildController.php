<?php

namespace App\Controller\Api;

use App\Entity\Activity;
use App\Entity\Child;
use App\Repository\ActivityRepository;
use App\Repository\ChildRepository;
use App\Service\PlanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChildController extends AbstractController
{
    #[Route('/api/{child}/{action}/{activity}', name: 'app_api_child_action_activity', methods: ["POST"])]
    public function index(
        Child $child,
        string $action,
        Activity $activity,
        ChildRepository $childRepository,
        ActivityRepository $activityRepository,
        PlanningService $planningService
    ): Response {
        // Voter Control
        $this->denyAccessUnlessGranted('CHILD_ACCESS', $child);

        if ($action === 'remove') {
            $child->removeActivity($activity);
        } elseif ($action === 'add') {
            $child->addActivity($activity);
        }

        $childRepository->add($child, true);

        $activities = $activityRepository->findByChild($child);
        $planning = $planningService->getPlanning($activities);

        return $this->json($planning);
    }
}
