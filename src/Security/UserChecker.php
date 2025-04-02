<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\ConfigRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private ConfigRepository $configRepository
    ) {}

    public function checkPreAuth(UserInterface $user): void {}

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Si l'utilisateur est ADMIN, il peut toujours se connecter
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return;
        }

        // Vérifier le code de l'utilisateur
        $code = $this->configRepository->findOneBy(['name' => 'code'])->getValue();
        if ($user->getCode() === null || $user->getCode() !== $code) {
            // Si le code est nul, ou différent, on lève une exception
            throw new AuthenticationException('ACCOUNT_EXPIRED');
        }
    }
}
