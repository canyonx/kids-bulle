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
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user');
        }

        return $this->render('home/index.html.twig', []);
    }
}
