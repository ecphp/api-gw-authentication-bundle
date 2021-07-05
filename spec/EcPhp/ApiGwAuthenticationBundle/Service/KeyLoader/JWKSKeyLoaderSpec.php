<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ecphp
 */

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Exception\ApiGwAuthenticationException;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader\JWKSKeyLoader;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader\KeyLoaderInterface;
use Exception;
use PhpSpec\ObjectBehavior;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class JWKSKeyLoaderSpec extends ObjectBehavior
{
    public function it_can_load_a_remote_key()
    {
        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn('foo');
    }

    public function it_can_throw_when_status_code_is_not_200(
        KeyLoaderInterface $keyLoader,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response,
        RequestInterface $request
    ) {
        $this->prepareDeps($keyLoader, $httpClient, $requestFactory, $keyConverter, $response, $request);

        $response
            ->getStatusCode()
            ->willReturn(500);

        $this
            ->shouldThrow(Exception::class)
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JWKSKeyLoader::class);

        $this
            ->getPublicKey()
            ->shouldReturn(KeyLoaderInterface::TYPE_PUBLIC);

        $this
            ->getSigningKey()
            ->shouldReturn(KeyLoaderInterface::TYPE_PRIVATE);

        $this
            ->getPassphrase()
            ->shouldReturn('passphrase');
    }

    public function it_throw_an_exception_when_the_jwks_has_no_keys(
        KeyLoaderInterface $keyLoader,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response,
        RequestInterface $request
    ) {
        $this->prepareDeps($keyLoader, $httpClient, $requestFactory, $keyConverter, $response, $request);

        $response
            ->getBody()
            ->willReturn(json_encode(['keys' => []]));

        $this
            ->shouldThrow(
                new ApiGwAuthenticationException(
                    'Invalid JWKS format of public key at public, keys array is empty.'
                )
            )
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function it_throw_an_exception_when_the_jwks_is_invalid(
        KeyLoaderInterface $keyLoader,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response,
        RequestInterface $request
    ) {
        $this->prepareDeps($keyLoader, $httpClient, $requestFactory, $keyConverter, $response, $request);

        $response
            ->getBody()
            ->willReturn(json_encode(['foo' => 'bar']));

        $this
            ->shouldThrow(
                new ApiGwAuthenticationException(
                    'Invalid JWKS format of public key at public.'
                )
            )
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function it_throw_an_exception_when_the_request_failed(
        KeyLoaderInterface $keyLoader,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response,
        RequestInterface $request
    ) {
        $this->prepareDeps($keyLoader, $httpClient, $requestFactory, $keyConverter, $response, $request);

        $httpClient
            ->sendRequest($request)
            ->willThrow(new ApiGwAuthenticationException('foo'));

        $this
            ->shouldThrow(ApiGwAuthenticationException::class)
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function let(
        KeyLoaderInterface $keyLoader,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response,
        RequestInterface $request
    ) {
        $this->prepareDeps($keyLoader, $httpClient, $requestFactory, $keyConverter, $response, $request);
        $this->beConstructedWith($keyLoader, $httpClient, $requestFactory, $keyConverter);
    }

    private function prepareDeps(
        KeyLoaderInterface $keyLoader,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response,
        RequestInterface $request
    ): void {
        $keyLoader
            ->getSigningKey()
            ->willReturn(KeyLoaderInterface::TYPE_PRIVATE);

        $keyLoader
            ->getPublicKey()
            ->willReturn(KeyLoaderInterface::TYPE_PUBLIC);

        $keyLoader
            ->getPassphrase()
            ->willReturn('passphrase');

        $response
            ->getStatusCode()
            ->willReturn(200);

        $response
            ->getBody()
            ->willReturn(
                json_encode([
                    'keys' => [
                        [
                            'jwks array structure',
                        ],
                    ],
                ])
            );

        $keyConverter
            ->fromJWKStoPEMS([['jwks array structure']])
            ->willReturn(['foo']);

        $requestFactory
            ->createRequest('GET', KeyLoaderInterface::TYPE_PUBLIC)
            ->willReturn(
                $request
            );

        $httpClient
            ->sendRequest($request)
            ->willReturn(
                $response
            );
    }
}
