<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class ApiGwKeyManager implements ApiGwKeyManagerInterface
{
    private HttpClientInterface $client;

    private array $configuration;

    private KeyConverterInterface $keyConverter;

    public function __construct(HttpClientInterface $client, KeyConverterInterface $keyConverter, array $configuration)
    {
        $this->client = $client;
        $this->keyConverter = $keyConverter;
        $this->configuration = $configuration;
    }

    public function getKeyPair(string $env): KeyPairInterface
    {
        // Todo: Obsolete, symfony configuration prevent empty config.
        $keyPair = $this->configuration['envs'][$env] ?? [];

        $failsafeKeyPair = $this->getFailsafeKeys($env);

        try {
            $keyPair['public'] = $this->client->request('GET', $keyPair['public'])->toArray();
        } catch (Throwable $e) {
            $keyPair['public'] = $failsafeKeyPair->getPublic()->getJWK();
        }

        // Todo: Find a better way to deal with private keys.
        if (null !== $keyPair['private']) {
            try {
                $keyPair['private'] = $this->client->request('GET', $keyPair['private'])->toArray();
            } catch (Throwable $e) {
                $keyPair['private'] = $failsafeKeyPair->getPrivate()->getJWK();
            }
        }

        return new KeyPair($this->keyConverter, ...array_values($keyPair));
    }

    private function getFailsafeKeys(string $env): KeyPairInterface
    {
        $keyPair = [
            'public' => sprintf('%s/../Resources/keys/%s/public.jwks.json', __DIR__, $env),
            'private' => sprintf('%s/../Resources/keys/%s/private.jwks.json', __DIR__, $env),
        ];

        if (true === file_exists($keyPair['public'])) {
            if (false !== $content = file_get_contents($keyPair['public'])) {
                $keyPair['public'] = json_decode($content, true);
            }
        }

        // Todo: Find a better way to deal with private keys.
        if (true === file_exists($keyPair['private'])) {
            if (false !== $content = file_get_contents($keyPair['private'])) {
                $keyPair['private'] = json_decode($content, true);
            }
        }

        return new KeyPair($this->keyConverter, ...array_values($keyPair));
    }
}
