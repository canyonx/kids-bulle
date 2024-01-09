<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\NewPasswordType;
use App\Form\LostPasswordType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = new User;
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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

    #[Route(path: '/register/lost-password', name: 'app_register_lost_password')]
    public function lostPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        MailerService $mailerService
    ) {
        $form = $this->createForm(LostPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy([
                'email' => $form["email"]->getData()
            ]);

            if (!$user) {
                $this->addFlash('success', 'Merci de bien vouloir vous enregistrer avant d\'oublier votre mot de passe');
                return $this->redirectToRoute('app_register');
            }

            //génère un Mot de passe aléatoire de 8 caratères
            $password = bin2hex(openssl_random_pseudo_bytes(4));
            $hash = $hasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            // Envoi un mail avec le new mdp
            $mailerService->send(
                $user->getEmail(),
                "Nouveau mot de passe",
                "emails/lost_password.html.twig",
                [
                    'password' => $password
                ]
            );

            $this->addFlash('success', 'Un nouveau mot de passe à été envoyé a votre adresse');
            return $this->redirectToRoute('app_login', []);
        }

        return $this->render('register/lost_password.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(path: '/register/edit-password', name: 'app_register_edit_password')]
    public function editPassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(NewPasswordType::class);
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

        return $this->render('register/edit_password.html.twig', [
            'form' => $form
        ]);
    }
}
