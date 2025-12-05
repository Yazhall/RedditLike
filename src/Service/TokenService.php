<?php

namespace App\Service;
use App\Entity\Token;
use App\Entity\User;



readonly class TokenService
{
    public function __construct(
        private MathService $mathService,
    ){

    }
    public function addTokenValue(int $size = 32): string
    {
        return bin2hex(random_bytes(floor($this->mathService->div($size, 2))));
    }
    public function createToken(User $user): Token
    {
        #voir si ce return marche proposition de l'IDE
        return (new Token())
            ->setUser($user)
            ->setTokenvalue($this->addTokenValue())
            ->setExpiresAt(
                (new \DateTime())
                ->add(new \DateInterval('PT10M'))
            );

    }

}
