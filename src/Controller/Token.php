<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Controller;

use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUser;
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

        $user = new ApiGwAuthenticationUser($username);

        return new JsonResponse(
            [
                'token' => $jwtManager->createFromPayload($user, $payload),
            ]
        );
    }
}
