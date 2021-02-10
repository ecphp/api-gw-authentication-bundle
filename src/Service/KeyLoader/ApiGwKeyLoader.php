<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Exception\ApiGwAuthenticationException;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

use function array_key_exists;

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

    public function loadKey($type): string
    {
        $publicKey = $this->getPublicKey();
        $signingKey = $this->getSigningKey();
        $passPhrase = $this->getPassphrase();

        $key = KeyLoaderInterface::TYPE_PUBLIC === $type ? $publicKey : $signingKey;

        if ('user' === $this->environment['env']) {
            $keyPathCandidates = [
                [$this->projectDir, $key], // Look in the App dir,
                [__DIR__, $key], // Look in this bundle dir,
                ['', $key], // Look whereever you want.
            ];

            foreach ($keyPathCandidates as $keyPathCandidateParts) {
                if (true === file_exists(implode('', $keyPathCandidateParts))) {
                    $prefix = current($keyPathCandidateParts);

                    return (new RawKeyLoader($prefix . $signingKey, $prefix . $publicKey, $passPhrase))
                        ->loadKey($type);
                }
            }
        }

        try {
            $key = (new JWKSKeyLoader($this, $this->httpClient, $this->keyConverter))
                ->loadKey($type);
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

        foreach (array_merge(self::$mapping, $envs) as $mapping) {
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

    private function loadFailsafeKey(string $type): string
    {
        $key = KeyLoaderInterface::TYPE_PUBLIC === $type ?
            $this->getFailsafePublicKey() :
            $this->getFailsafePrivateKey();

        // Todo: Remove duplicated code in here and JWKSKeyLoader.
        $jwksArray = json_decode(file_get_contents($key), true);

        if (false === array_key_exists('keys', $jwksArray)) {
            throw new ApiGwAuthenticationException(
                sprintf('Invalid JWKS format of %s key at %s.', $type, $key)
            );
        }

        if ([] === $jwksArray['keys']) {
            throw new ApiGwAuthenticationException(
                sprintf('Invalid JWKS format of %s key at %s, keys array is empty.', $type, $key)
            );
        }

        return current($this->keyConverter->fromJWKStoPEMS($jwksArray['keys']));
    }
}
