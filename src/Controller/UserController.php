<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profil")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user", methods={"GET"})
     */
    public function show(ActivityRepository $activityRepository): Response
    {
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = new \DateTimeImmutable("today + $i days");
        }

        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
            'dates' => $dates,
            'activities' => $activityRepository->findByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User */
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // For security hash password
            $pwd = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($pwd);

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete", name="app_user_delete", methods={"POST"})
     */
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
