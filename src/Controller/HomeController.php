<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Service\PlanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(
        CategoryRepository $categoryRepository,
        ActivityRepository $activityRepository,
        PlanningService $planningService
    ): Response {
        $activities = $activityRepository->findByDateBetween();

        $planning = $planningService->getPlanning($activities);

        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['number' => 'ASC']),
            'planning' => $planning
        ]);
    }
}
