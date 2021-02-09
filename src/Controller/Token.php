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

        $payload = [
            'iat' => time(),
            'sub' => $username,
            'jti' => uniqid(),
            'iss' => '/api/token',
            'foo' => 'bar',
        ];

        $user = new ApiGwAuthenticatorUser($username);

        return new JsonResponse(
            [
                'token' => $jwtManager->createFromPayload($user, $payload),
            ]
        );
    }
}
