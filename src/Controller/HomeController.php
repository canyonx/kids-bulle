<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\MailerService;
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

    /**
     * @Route("/email", name="app_email")
     */
    public function email(
        MailerService $mailerService,
        UserRepository $userRepository
    ): Response {
        // $mailerService->send('dauchez.matteo@oclock.school', 'test', 'message_notification', []);
        // /** @var User */
        // $user = $this->getUser();

        // $user->setIsVerified(true);
        // $userRepository->add($user, true);
        return $this->redirectToRoute('app_home');
    }
}
