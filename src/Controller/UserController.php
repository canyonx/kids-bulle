<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ChangePasswordType;
use App\Service\PlanningService;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use App\Service\StartDateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    public function activities(
        ActivityRepository $activityRepository,
        PlanningService $planningService,
        StartDateService $startDateService,
        Request $request
    ): Response {
        /** @var User */
        $user = $this->getUser();

        // Set correct start date 
        $dateStart = $startDateService->getStartDate($request->get('date'));

        if ($this->isGranted('ROLE_TEACHER')) {
            // Get araay of teacher and childs activities
            $aTeacher = $activityRepository->findByTeacher($this->getUser(), $dateStart);
            $aChilds = $activityRepository->findByUser($this->getUser(), $dateStart);
            // Merge two arrrays and delete same antries
            $activities = array_unique(array_merge($aTeacher, $aChilds));
        } else {
            // Get array of child activities
            $activities = $activityRepository->findByUser($this->getUser(), $dateStart);
        }

        $planning = $planningService->getPlanning($activities, $dateStart);

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

        return $this->render('user/edit.html.twig', [
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

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/edit-password', name: 'app_user_edit_password')]
    public function editPassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form["newPassword"]->getData();
            $hash = $hasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Le mot de passe à été changé');
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form
        ]);
    }
}
