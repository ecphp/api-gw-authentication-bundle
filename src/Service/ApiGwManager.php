<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

use Firebase\JWT\JWT;

final class ApiGwManager implements ApiGwManagerInterface
{
    private JWT $jwt;

    public function __construct(JWT $jwt, array $configuration)
    {
        $this->jwt = $jwt;
        $this->configuration = $configuration;
    }

    public function decode(string $jwt): array
    {
        return (array) $this->jwt::decode(
            $jwt,
            $this->getPublicKey(),
            ['RS256']
        );
    }

    private function getPublicKey(): string
    {
        return file_get_contents(
            sprintf(
                '%s/../Resources/keys/%s/public.pem',
                __DIR__,
                $this->configuration['env']
            )
        );
    }
}
