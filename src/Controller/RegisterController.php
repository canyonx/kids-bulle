<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LostPasswordType;
use App\Service\MailerService;
use App\Form\ResetPasswordType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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

    /**
     * Lost Password
     */
    #[Route(path: '/register/lost-password', name: 'app_register_lost_password')]
    public function lostPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        TokenGeneratorInterface $tokenGenerator,
        MailerService $mailerService
    ) {
        $form = $this->createForm(LostPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form["email"]->getData()]);

            if (!$user) {
                $this->addFlash('success', 'Merci de bien vouloir vous enregistrer avant d\'oublier votre mot de passe');
                return $this->redirectToRoute('app_register');
            }

            //génère un Url avec token aléatoire
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $em->flush();
            $url = $this->generateUrl('app_register_new_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            // Envoi un mail avec le new mdp
            $mailerService->send(
                $user->getEmail(),
                "Nouveau mot de passe",
                "lost_password",
                [
                    'url' => $url
                ]
            );

            $this->addFlash('success', 'Un lien de changement de mot de passe à été envoyé a votre adresse');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/lost_password.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Password reinitialisation
     */
    #[Route(path: '/register/new-password/{token}', name: 'app_register_new_password')]
    public function newPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if ($user) {

            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $user->setResetToken('');
                $password = $hasher->hashpassword($user, $form->get('newPassword')->getData());
                $user->setPassword($password);
                $em->flush();

                //addflash
                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('register/reset_password.html.twig', [
                'form' => $form
            ]);
        }
    }
}
