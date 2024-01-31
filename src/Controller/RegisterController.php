<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    /**
     * Register
     *
     */
    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = new User;
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $form["code"]->getData() === $this->getParameter('register_code')) {
            $password = $form["plainPassword"]->getData();
            $hash = $hasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte à été crée, vous pouvez vous connecter');
            return $this->redirectToRoute('app_home', []);
        }

        return $this->render('register/register.html.twig', [
            'registrationForm' => $form
        ]);
    }
}
