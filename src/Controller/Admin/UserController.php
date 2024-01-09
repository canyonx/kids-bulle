<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/user')]
class UserController extends AbstractController
{
    #[Route(path: '/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findBy([], ['roles' => 'DESC']),
        ]);
    }

    // #[Route(path: '/new', name: 'app_admin_user_new')]
    // public function new(
    //     Request $request,
    //     UserPasswordHasherInterface $passwordHasher,
    //     UserRepository $userRepository
    // ): Response {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
    //         // $user->setRoles(['ROLE_USER']);
    //         $userRepository->add($user, true);
    //         return $this->redirectToRoute('app_admin_user_index');
    //     }

    //     return $this->renderForm('admin/user/new.html.twig', [
    //         'form' => $form,
    //     ]);
    // }

    #[Route(path: '/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user, ['option' => 'edit_role']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([$form['role']->getData()]);
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
