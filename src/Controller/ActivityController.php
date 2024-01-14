<?php

namespace App\Controller;

use App\Entity\Activity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/activity')]
class ActivityController extends AbstractController
{
    #[Route(path: '/{id}', name: 'app_activity_show', methods: ['GET', 'POST'])]
    public function show(
        Activity $activity
    ): Response {
        return $this->render('activity/show.html.twig', [
            'activity' => $activity
        ]);
    }
}
