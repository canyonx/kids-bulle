<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ChildVoter extends Voter
{
    public const ACCESS = 'CHILD_ACCESS';
    public const ADMIN = 'CHILD_ADMIN';

    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ACCESS])
            && $subject instanceof \App\Entity\Child;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if ($this->security->isGranted('ROLE_ADMIN')) {
        //     return true;
        // }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ACCESS:
                if ($user === $subject->getUser()) return true;
                break;
            case self::ADMIN:
                if ($this->security->isGranted('ROLE_ADMIN')) return true;
                break;
        }

        return false;
    }
}
