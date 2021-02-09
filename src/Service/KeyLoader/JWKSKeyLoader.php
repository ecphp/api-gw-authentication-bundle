<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JWKSKeyLoader implements KeyLoaderInterface
{
    private HttpClientInterface $httpClient;

    private KeyConverterInterface $keyConverter;

    private KeyLoaderInterface $keyLoader;

    public function __construct(
        KeyLoaderInterface $keyLoader,
        HttpClientInterface $httpClient,
        KeyConverterInterface $keyConverter
    ) {
        $this->keyLoader = $keyLoader;
        $this->httpClient = $httpClient;
        $this->keyConverter = $keyConverter;
    }

    public function getPassphrase(): string
    {
        return $this->keyLoader->getPassphrase();
    }

    public function getPublicKey(): string
    {
        return $this->keyLoader->getPublicKey();
    }

    public function getSigningKey(): string
    {
        return $this->keyLoader->getSigningKey();
    }

    public function loadKey($type)
    {
        try {
            $jwks = $this->httpClient->request('GET', $this->keyLoader->getPublicKey());
        } catch (TransportExceptionInterface $e) {
            throw $e;
        }

        if ($jwks->getStatusCode() !== 200) {
            throw new Exception('Foo');
        }

        $keys = $this->keyConverter->fromJWKStoPEMS($jwks->toArray()['keys']);

        return current($keys);
    }
}
