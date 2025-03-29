<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Service\PlanningService;
use App\Service\StartDateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function index(
        CategoryRepository $categoryRepository,
        ActivityRepository $activityRepository,
        PlanningService $planningService,
        StartDateService $startDateService,
        Request $request
    ): Response {
        // Set correct start date
        $dateStart = $startDateService->getStartDate($request->get('date'));

        $activities = $activityRepository->findByDateBetween($dateStart);

        $planning = $planningService->getPlanning($activities, $dateStart);


        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['number' => 'ASC']),
            'planning' => $planning
        ]);
    }
}
