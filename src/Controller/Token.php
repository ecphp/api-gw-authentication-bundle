<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Controller;

use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class Token
{
    public function __invoke(JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $username = uniqid('user_');

        $user = new ApiGwAuthenticatorUser(
            $username,
            [
                'iat' => time(),
                'sub' => $username,
                'jti' => uniqid(),
                'iss' => '/api/token',
                'foo' => 'bar',
            ]
        );

        return new JsonResponse(
            [
                'token' => $jwtManager->create($user),
            ]
        );
    }
}
