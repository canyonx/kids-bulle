<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ActivityRepository $activityRepository): Response
    {
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = new \DateTimeImmutable("today + $i days");
        }

        $activities = $activityRepository->findByDateBetween(new \DateTimeImmutable("today"));

        return $this->render('home/index.html.twig', [
            'dates' => $dates,
            'activities' => $activities
        ]);
    }
}
