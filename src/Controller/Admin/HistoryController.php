<?php

namespace App\Controller\Admin;

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
        $dates = $activityRepository->findAllMonths();

        $history = [];

        foreach ($dates as $date) {
            // $date est un DateTimeImmutable 
            $year = (int) $date->format('Y');
            $month = (int) $date->format('n');

            // Initialise l'année avec les 12 mois à false (une seule fois)
            if (!isset($history[$year])) {
                for ($i = 1; $i <= 12; $i++) {
                    $history[$year][$i] = false;
                }
            }

            // Marque le mois comme ayant une activité
            $history[$year][$month] = true;
        }

        return $this->render('admin/history/index.html.twig', [
            'history' => $history,
        ]);
    }
}
