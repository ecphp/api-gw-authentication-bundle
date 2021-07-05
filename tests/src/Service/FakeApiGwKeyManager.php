<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ecphp
 */

declare(strict_types=1);

namespace tests\EcPhp\ApiGwAuthenticationBundle\Service;

use EcPhp\ApiGwAuthenticationBundle\Service\ApiGwKeyManagerInterface;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverterInterface;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyPair;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FakeApiGwKeyManager implements ApiGwKeyManagerInterface
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

    public function getKeyPair(string $env): KeyPair
    {
        // Todo: Obsolete, symfony configuration prevent empty config.
        $keyPair = $this->configuration['envs'][$env] ?? [];

        $failsafeKeyPair = $this->getFailsafeKeys($env);

        $keyPair['public'] = $failsafeKeyPair->getPublic()->getJWK();

        // Todo: Find a better way to deal with private keys.
        if (null !== $keyPair['private']) {
            $keyPair['private'] = $failsafeKeyPair->getPrivate()->getJWK();
        }

        return new KeyPair($this->keyConverter, ...array_values($keyPair));
    }

    private function getFailsafeKeys(string $env): KeyPair
    {
        $keyPair = [
            'public' => sprintf('%s/../Resources/keys/%s/public.jwks.json', __DIR__, $env),
            'private' => sprintf('%s/../Resources/keys/%s/private.key', __DIR__, $env),
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
