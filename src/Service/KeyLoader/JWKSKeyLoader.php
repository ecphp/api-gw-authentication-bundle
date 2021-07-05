<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ecphp
 */

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Exception\ApiGwAuthenticationException;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Throwable;

use function array_key_exists;

final class JWKSKeyLoader implements KeyLoaderInterface
{
    private ClientInterface $httpClient;

    private KeyConverterInterface $keyConverter;

    private KeyLoaderInterface $keyLoader;

    private RequestFactoryInterface $requestFactory;

    public function __construct(
        KeyLoaderInterface $keyLoader,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $this->keyLoader = $keyLoader;
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
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

    public function loadKey($type): string
    {
        // Todo: Implements for PRIVATE key as well.
        $key = $this->keyLoader->getPublicKey();

        try {
            $response = $this
                ->httpClient
                ->sendRequest(
                    $this
                        ->requestFactory
                        ->createRequest(
                            'GET',
                            $key
                        )
                );
        } catch (Throwable $e) {
            throw new ApiGwAuthenticationException(
                sprintf('Unable to request uri(%s) for %s key.', $key, $type),
                $e->getCode(),
                $e
            );
        }

        if (200 !== $statusCode = $response->getStatusCode()) {
            throw new ApiGwAuthenticationException(
                sprintf('Invalid code(%s) thrown while fetching the %s key at %s.', $statusCode, $type, $key)
            );
        }

        $jwks = (array) json_decode((string) $response->getBody(), true);

        if (false === array_key_exists('keys', $jwks)) {
            throw new ApiGwAuthenticationException(
                sprintf('Invalid JWKS format of %s key at %s.', $type, $key)
            );
        }

        if ([] === $jwks['keys']) {
            throw new ApiGwAuthenticationException(
                sprintf('Invalid JWKS format of %s key at %s, keys array is empty.', $type, $key)
            );
        }

        return current($this->keyConverter->fromJWKStoPEMS($jwks['keys']));
    }
}
