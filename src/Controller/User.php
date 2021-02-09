<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Controller;

use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

final class User
{
    public function __invoke(Security $security, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $user = $security->getUser();

        if ($user instanceof ApiGwAuthenticationUserInterface) {
            return new JsonResponse($user->getAttributes());
        }

        return new JsonResponse([], 404);
    }
}
