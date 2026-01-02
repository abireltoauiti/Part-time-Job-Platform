<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Vérifie si l'utilisateur est actif
        if (!$user->isActive()) {
            // Le message sera affiché à l'utilisateur
            throw new CustomUserMessageAccountStatusException('Votre compte a été désactivé! Veuillez contacter un administrateur.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien à faire après l'authentification
    }
}
