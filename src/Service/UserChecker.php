<?php

namespace App\Service;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user):void
    {
        if (!$user->getIsVerified()){
            throw new CustomUserMessageAuthenticationException('Veuillez vérifier votre email avant de vous connecter.');
        }
        if (!$user->isEnabled()) {
            throw new CustomUserMessageAuthenticationException('Votre compte est désactivé. Contactez le support.');
        }

    }
    public function checkPostAuth(UserInterface $user):void
    {

    }


}
