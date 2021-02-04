<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

use Firebase\JWT\JWT;

final class ApiGwManager implements ApiGwManagerInterface
{
    private ApiGwKeyManager $apiGwKeyManager;

    private JWT $jwt;

    public function __construct(JWT $jwt, ApiGwKeyManager $apiGwKeyManager, array $configuration)
    {
        $this->jwt = $jwt;
        $this->apiGwKeyManager = $apiGwKeyManager;
        $this->configuration = $configuration;
    }

    public function decode(string $jwt): array
    {
        return (array) $this->jwt::decode(
            $jwt,
            $this->getPublicKey(),
            ['RS256'] // Todo: Nice to have: This should come from configuration
        );
    }

    public function encode(string $jwt): array
    {
        return (array) $this->jwt::encode(
            $jwt,
            $this->getPrivateKey(),
            ['RS256'] // Todo: Nice to have: This should come from configuration
        );
    }

    private function getPrivateKey(): string
    {
        return $this->apiGwKeyManager->getKeyPair($this->configuration['defaults']['env'])->getPrivate()->getPEM();
    }

    private function getPublicKey(): string
    {
        return $this->apiGwKeyManager->getKeyPair($this->configuration['defaults']['env'])->getPublic()->getPEM();
    }
}
