<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

/**
 * Class ApiGwKeyLoader.
 *
 * phpcs:disable Generic.Files.LineLength.TooLong
 */
final class ApiGwKeyLoader implements KeyLoaderInterface
{
    private array $configuration;

    private array $environment;

    private HttpClientInterface $httpClient;

    private KeyConverterInterface $keyConverter;

    private static array $mapping = [
        [
            'env' => 'production',
            KeyLoaderInterface::TYPE_PUBLIC => 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
            KeyLoaderInterface::TYPE_PRIVATE => '',
            'failsafe' => [
                KeyLoaderInterface::TYPE_PUBLIC => __DIR__ . '/../../Resources/keys/production/jwks.json',
                KeyLoaderInterface::TYPE_PRIVATE => '',
            ],
        ],
        [
            'env' => 'intra',
            KeyLoaderInterface::TYPE_PUBLIC => 'https://intrapi.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
            KeyLoaderInterface::TYPE_PRIVATE => '',
            'failsafe' => [
                KeyLoaderInterface::TYPE_PUBLIC => __DIR__ . '/../../Resources/keys/intra/jwks.json',
                KeyLoaderInterface::TYPE_PRIVATE => '',
            ],
        ],
        [
            'env' => 'acceptance',
            KeyLoaderInterface::TYPE_PUBLIC => 'https://api.acceptance.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
            KeyLoaderInterface::TYPE_PRIVATE => '',
            'failsafe' => [
                KeyLoaderInterface::TYPE_PUBLIC => __DIR__ . '/../../Resources/keys/acceptance/jwks.json',
                KeyLoaderInterface::TYPE_PRIVATE => '',
            ],
        ],
    ];

    private string $projectDir;

    public function __construct(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, string $projectDir, array $configuration = [])
    {
        $this->httpClient = $httpClient;
        $this->keyConverter = $keyConverter;
        $this->projectDir = $projectDir;
        $this->configuration = $configuration;
        $this->environment = $this->getEnvironment($configuration['defaults']['env']);
    }

    public function getPassphrase(): string
    {
        return '';
    }

    public function getPublicKey(): string
    {
        return $this->environment[KeyLoaderInterface::TYPE_PUBLIC] ?? '';
    }

    public function getSigningKey(): string
    {
        return $this->environment[KeyLoaderInterface::TYPE_PRIVATE] ?? '';
    }

    public function loadKey($type)
    {
        $key = $this->getKey($type);

        if (true === file_exists($this->projectDir . $key)) {
            return (new RawKeyLoader($this->projectDir . $this->getSigningKey(), $this->projectDir . $this->getPublicKey(), $this->getPassphrase()))->loadKey($type);
        }

        if (true === file_exists(__DIR__ . $key)) {
            return (new RawKeyLoader(__DIR__ . $this->getSigningKey(), __DIR__ . $this->getPublicKey(), $this->getPassphrase()))->loadKey($type);
        }

        if (true === file_exists($key)) {
            return (new RawKeyLoader($this->getSigningKey(), $this->getPublicKey(), $this->getPassphrase()))->loadKey($type);
        }

        $keyLoader = new JWKSKeyLoader($this, $this->httpClient, $this->keyConverter);

        try {
            $key = $keyLoader->loadKey($type);
        } catch (TransportExceptionInterface $e) {
            $key = $this->loadFailsafeKey($type);
        } catch (Throwable $e) {
            $key = $this->loadFailsafeKey($type);
        }

        return $key;
    }

    private function getEnvironment(string $env): array
    {
        $envs = [];

        foreach ($this->configuration['envs'] as $name => $data) {
            $envs[] = [
                'env' => $name,
                KeyLoaderInterface::TYPE_PUBLIC => $data[KeyLoaderInterface::TYPE_PUBLIC],
                KeyLoaderInterface::TYPE_PRIVATE => $data[KeyLoaderInterface::TYPE_PRIVATE],
                'failsafe' => [
                    KeyLoaderInterface::TYPE_PUBLIC => $data['failsafe'][KeyLoaderInterface::TYPE_PUBLIC] ?? $data[KeyLoaderInterface::TYPE_PUBLIC],
                    KeyLoaderInterface::TYPE_PRIVATE => $data['failsafe'][KeyLoaderInterface::TYPE_PRIVATE] ?? $data[KeyLoaderInterface::TYPE_PRIVATE],
                ],
            ];
        }

        foreach (array_merge($envs, self::$mapping) as $mapping) {
            if ($mapping['env'] === $env) {
                return $mapping;
            }
        }
    }

    private function getFailsafePrivateKey(): string
    {
        return $this->environment['failsafe'][KeyLoaderInterface::TYPE_PRIVATE];
    }

    private function getFailsafePublicKey(): string
    {
        return $this->environment['failsafe'][KeyLoaderInterface::TYPE_PUBLIC];
    }

    private function getKey(string $type): string
    {
        return KeyLoaderInterface::TYPE_PUBLIC === $type ?
            $this->getPublicKey() :
            $this->getSigningKey();
    }

    private function loadFailsafeKey(string $type): string
    {
        $key = KeyLoaderInterface::TYPE_PUBLIC === $type ?
            $this->getFailsafePublicKey() :
            $this->getFailsafePrivateKey();

        $jwks = json_decode(file_get_contents($key), true);

        $keys = $this->keyConverter->fromJWKStoPEMS($jwks['keys']);

        return current($keys);
    }
}
