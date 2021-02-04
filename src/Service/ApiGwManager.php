<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

use Firebase\JWT\JWT;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class ApiGwManager implements ApiGwManagerInterface
{
    private array $configuration;

    private HttpClientInterface $httpClient;

    private JWT $jwt;

    private KeyConverterInterface $keyConverter;

    private string $projectDir;

    public function __construct(HttpClientInterface $httpClient, JWT $jwt, KeyConverterInterface $keyConverter, array $configuration, string $projectDir)
    {
        $this->jwt = $jwt;
        $this->httpClient = $httpClient;
        $this->keyConverter = $keyConverter;
        $this->configuration = $configuration;
        $this->projectDir = $projectDir;
    }

    public function decode(string $jwt): array
    {
        return (array) $this->jwt::decode(
            $jwt,
            (string) $this->getPublicKey(),
            ['RS256'] // Todo: Nice to have: This should come from configuration
        );
    }

    public function encode(array $payload): string
    {
        return $this->jwt::encode(
            $payload,
            (string) $this->getPrivateKey(),
            'RS256' // Todo: Nice to have: This should come from configuration
        );
    }

    public function getKeyPair(string $env): KeyPairInterface
    {
        // Todo: Obsolete, symfony configuration prevent empty config.
        $keyPair = $this->configuration['envs'][$env] ?? [];
        $failsafeKeyPair = $this->getFailsafeKeypair($env);

        foreach ($keyPair as $type => $source) {
            if (null === $source) {
                $keyPair[$type] = '';

                continue;
            }

            if (true === file_exists($this->projectDir . $source) && false !== $content = file_get_contents($this->projectDir . $source)) {
                $keyPair[$type] = $content;

                continue;
            }

            if (true === file_exists($source) && false !== $content = file_get_contents($source)) {
                $keyPair[$type] = $content;

                continue;
            }

            try {
                $key = $this->httpClient->request('GET', $source)->toArray();

                $keyPair[$type] = $this->keyConverter->toPEM(current($key['keys']));
            } catch (Throwable $e) {
                $keyPair[$type] = (string) $failsafeKeyPair->getPublic();
            }
        }

        return new KeyPair(new Key($keyPair['public']), new Key($keyPair['private']));
    }

    private function getFailsafeKeypair(string $env): KeyPairInterface
    {
        $keyPair = array_map(
            static function (string $filepath): string {
                if (false === file_exists($filepath)) {
                    return '';
                }

                if (false === $content = file_get_contents($filepath)) {
                    return '';
                }

                return $content;
            },
            [
                sprintf('%s/../Resources/keys/%s/public.key', __DIR__, $env),
                sprintf('%s/../Resources/keys/%s/private.key', __DIR__, $env),
            ]
        );

        return new KeyPair(new Key($keyPair[0]), new Key($keyPair[1]));
    }

    private function getPrivateKey(): KeyInterface
    {
        return $this
            ->getKeyPair($this->configuration['defaults']['env'])
            ->getPrivate();
    }

    private function getPublicKey(): KeyInterface
    {
        return $this
            ->getKeyPair($this->configuration['defaults']['env'])
            ->getPublic();
    }
}
