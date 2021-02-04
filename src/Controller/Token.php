<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Controller;

use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class Token
{
    public function __invoke(ApiGwManagerInterface $apiGwManager): JsonResponse
    {
        return new JsonResponse(
            [
                'token' => $apiGwManager->encode(
                    [
                        'iat' => time(),
                        'sub' => 'foo',
                        'jti' => uniqid(),
                        'iss' => '/api/token',
                    ]
                ),
            ]
        );
    }
}
