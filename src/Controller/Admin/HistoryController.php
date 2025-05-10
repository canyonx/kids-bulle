<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**
     * Affiche les années pour lesquelles il y a des activités
     */
    #[Route(path: '/admin/history', name: 'app_admin_history', methods: ['GET'])]
    public function index(ActivityRepository $activityRepository): Response
    {
        $dates = $activityRepository->findAllDateSql();

        $history = [];

        foreach ($dates as $date) {
            // $date est un DateTimeImmutable
            $year = (int) $date->format('Y');
            $month = (int) $date->format('n'); // mois sans zéro initial (1–12)

            // Évite les doublons
            $history[$year][$month] = true;
        }

        return $this->render('admin/history/index.html.twig', [
            'history' => $history,
        ]);
    }
}
