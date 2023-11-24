<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\NewPasswordType;
use App\Form\LostPasswordType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
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

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->renderForm('register/register.html.twig', [
            'registrationForm' => $form
        ], $response);
    }

    #[Route(path: '/register/lost-password', name: 'app_register_lost_password')]
    public function lostPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        MailerInterface $mailer
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

            // TODO : use mailer service
            // Envoi un mail avec le new mdp
            $email = new TemplatedEmail;
            // Expéditeur
            $email->from(new Address("contact@mail.com", "Infos de la boutique"))
                // Destinataire
                ->to($user->getEmail())
                // contenu
                ->text("Votre nouveau mot de passe" . $password)
                ->htmlTemplate("emails/lost_password.html.twig")
                ->context([
                    'password' => $password
                ])
                // sujet
                ->subject("Nouveau mot de passe");
            // Envoi du mail
            $mailer->send($email);

            $this->addFlash('success', 'Un nouveau mot de passe à été envoyé a votre adresse');
            return $this->redirectToRoute('app_login', []);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->renderForm('register/lost_password.html.twig', [
            'form' => $form
        ], $response);
    }

    #[Route(path: '/register/edit-password', name: 'app_register_edit_password')]
    #[IsGranted('ROLE_USER')]
    public function editPassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
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

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->renderForm('register/edit_password.html.twig', [
            'form' => $form
        ], $response);
    }
}
