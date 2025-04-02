<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ReactivateType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\ConfigRepository;
use App\Security\AppLoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

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
        UserPasswordHasherInterface $hasher,
        ConfigRepository $configRepository,
    ): Response {
        $user = new User;
        $form = $this->createForm(RegistrationFormType::class, $user);

        // object config, code d'activation de la base de données
        $code = $configRepository->findOneBy(['name' => 'code']);

        $form->handleRequest($request);
        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            // Verification du code d'activation
            // $form["code"]->getData() === $this->getParameter('register_code')
            $form["code"]->getData() === $code->getValue()
        ) {
            $password = $form["plainPassword"]->getData();
            $hash = $hasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $user->setCode($code->getValue());
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
     * Activation route
     */
    #[Route(path: '/register/reactivate/{id}', name: 'app_register_reactivate')]
    public function activation(
        int $id,
        Request $request,
        UserRepository $userRepository,
        AppLoginAuthenticator $appLoginAuthenticator,
        UserAuthenticatorInterface $userAuthenticator,
        ConfigRepository $configRepository
    ): Response {
        /** @var User */
        $user = $userRepository->find($id);

        $form = $this->createForm(ReactivateType::class, $user);
        $form->handleRequest($request);

        // object config, code d'activation de la base de données
        $code = $configRepository->findOneBy(['name' => 'code']);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCode = $form["code"]->getData();

            if ($userCode === $code->getValue()) {
                // Activation de l'utilisateur
                $user->setCode($code->getValue());
                $userRepository->add($user, true);
                $this->addFlash('success', 'Votre compte a été réactivé.');

                // Authentifier l'utilisateur
                return $userAuthenticator->authenticateUser(
                    $user,
                    $appLoginAuthenticator,
                    $request
                );

                // return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('error', 'Le code d\'activation est invalide.');
            }
        }

        return $this->render('register/reactivate.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}
