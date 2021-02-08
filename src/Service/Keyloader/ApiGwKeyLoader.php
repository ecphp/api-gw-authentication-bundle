<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service\Keyloader;

use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverter\KeyConverterInterface;
use Firebase\JWT\JWK;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ApiGwKeyLoader implements KeyLoaderInterface
{
    private array $configuration;

    private HttpClientInterface $httpClient;

    private KeyConverterInterface $keyConverter;

    private static $mapping = [
        [
            'env' => 'production',
            'uri' => 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
            'failsafe' => __DIR__ . '/../Resources/keys/production/jwks.json',
        ],
        [
            'env' => 'intra',
            'uri' => 'https://intrapi.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
            'failsafe' => __DIR__ . '/../Resources/keys/intra/jwks.json',
        ],
        [
            'env' => 'acceptance',
            'uri' => 'https://api.acceptance.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
            'failsafe' => __DIR__ . '/../Resources/keys/acceptance/jwks.json',
        ],
    ];

    public function __construct(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, array $configuration = [])
    {
        $this->httpClient = $httpClient;
        $this->keyConverter = $keyConverter;
        $this->configuration = $configuration;
    }

    public function getPassphrase()
    {
        return null;
    }

    public function getPublicKey()
    {
        foreach (self::$mapping as $mapping) {
            if ($mapping['env'] === $this->configuration['defaults']['env']) {
                return $mapping['uri'];
            }
        }
    }

    public function getSigningKey()
    {
        return null;
    }

    public function loadKey($type)
    {
        $keyLoader = new JWKSKeyLoader($this, $this->httpClient, $this->keyConverter);

        try {
            $key = $keyLoader->loadKey($type);
        } catch (TransportExceptionInterface $e) {
            $key = $this->getFailsafeKey($this->getPublicKey());
        }

        return $key;
    }

    private function getFailsafeKey(string $uri): string
    {
        $jwks = json_decode(file_get_contents($this->getFailsafeLocalKey($uri)), true);

        $keys = JWK::parseKeySet($jwks);
        $key = current($keys);

        return openssl_pkey_get_details($key)['key'];
    }

    private function getFailsafeLocalKey(string $uri): string
    {
        foreach (self::$mapping as $mapping) {
            if ($mapping['uri'] === $uri) {
                return $mapping['failsafe'];
            }
        }
    }
}
