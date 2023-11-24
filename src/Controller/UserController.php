<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use App\Service\PlanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profil')]
class UserController extends AbstractController
{
    #[Route(path: '/', name: 'app_user', methods: ['GET'])]
    public function show(ActivityRepository $activityRepository, PlanningService $planningService): Response
    {
        $activities = $activityRepository->findByUser($this->getUser());

        $planning = $planningService->getPlanning($activities);

        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
            'planning' => $planning
        ]);
    }

    #[Route(path: '/activities', name: 'app_user_activities', methods: ['GET'])]
    public function activities(ActivityRepository $activityRepository, PlanningService $planningService): Response
    {
        /** @var User */
        $user = $this->getUser();

        $aTeacher = $activityRepository->findByTeacher($this->getUser());
        $aChilds = $activityRepository->findByUser($this->getUser());

        // Merge two arrrays and delete same antries
        $activities = array_unique(array_merge($aTeacher, $aChilds));

        $planning = $planningService->getPlanning($activities);

        return $this->render('user/activities.html.twig', [
            'user' => $user,
            'childs' => $user->getChilds(),
            'planning' => $planning
        ]);
    }

    #[Route(path: '/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User */
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route(path: '/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, UserRepository $userRepository): Response
    {
        /** @var User */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
    }
}
