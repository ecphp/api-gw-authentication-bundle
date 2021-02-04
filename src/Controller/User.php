<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Controller;

use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwKeyManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

final class User
{
    public function __invoke(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if ($user instanceof ApiGwAuthenticatorUserInterface) {
            return new JsonResponse($user->getAttributes());
        }

        return new JsonResponse([]);
    }

    public function token(ApiGwKeyManagerInterface $apiGwKeyManager): JsonResponse
    {
        return new JsonResponse(
            $apiGwKeyManager
                ->getKeyPair('test')
        );
    }
}
