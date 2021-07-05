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
use EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader\ApiGwKeyLoader;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiGwKeyLoaderSpec extends ObjectBehavior
{
    public function it_can_get_the_api_gateway_production_failsafe_key(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response,
        RequestInterface $request
    ) {
        $configuration = [
            'defaults' => [
                'env' => 'production',
            ],
            'envs' => [],
        ];

        $projectDir = __DIR__;

        $response
            ->getStatusCode()
            ->willReturn(500);

        $response
            ->getContent()
            ->willReturn('');

        $requestFactory
            ->createRequest('GET', 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json')
            ->willReturn($request);

        $httpClient
            ->sendRequest(Argument::type(RequestInterface::class))
            ->willReturn($response);

        $key = file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/public.key');
        $jwksArray = json_decode(file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/jwks.json'), true);
        $keyConverter
            ->fromJWKStoPEMS($jwksArray['keys'])
            ->willReturn([
                $key,
            ]);

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->getPublicKey()
            ->shouldReturn('https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn($key);
    }

    public function it_can_get_the_api_gateway_production_key(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response
    ) {
        $configuration = [
            'defaults' => [
                'env' => 'production',
            ],
            'envs' => [],
        ];

        $projectDir = __DIR__;

        $response
            ->getStatusCode()
            ->willReturn(200);

        $jwksArray = json_decode(file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/jwks.json'), true);
        $response
            ->toArray()
            ->willReturn($jwksArray);

        $httpClient
            ->sendRequest(Argument::type(RequestInterface::class))
            ->willReturn($response);

        $key = file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/public.key');
        $keyConverter
            ->fromJWKStoPEMS($jwksArray['keys'])
            ->willReturn([
                $key,
            ]);

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->getPublicKey()
            ->shouldReturn('https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn($key);
    }

    public function it_can_get_user_failsafe_private_key(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $publicKeyFilepath = '/../../../tests/src/Resources/keys/user/public.jwks.json';
        $privateKeyFilepath = '/../../../tests/src/Resources/keys/user/private.jwks.json';
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => 'http://a.b.c.d.e.f',
                    KeyLoaderInterface::TYPE_PRIVATE => 'http://a.b.c.d.e.f',
                    'failsafe' => [
                        KeyLoaderInterface::TYPE_PUBLIC => $publicKeyFilepath,
                        KeyLoaderInterface::TYPE_PRIVATE => $privateKeyFilepath,
                    ],
                ],
            ],
        ];

        $projectDir = __DIR__;

        $jwks = json_decode(file_get_contents(__DIR__ . '/../..' . $privateKeyFilepath), true);

        $keyConverter
            ->fromJWKStoPEMS($jwks['keys'])
            ->willReturn([
                'foo',
            ]);

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PRIVATE)
            ->shouldReturn('foo');
    }

    public function it_can_get_user_local_private_key(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $publicKeyFilepath = '/../../../tests/src/Resources/keys/user/public.jwks.json';
        $privateKeyFilepath = '/../../../tests/src/Resources/keys/user/private.jwks.json';
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => $publicKeyFilepath,
                    KeyLoaderInterface::TYPE_PRIVATE => $privateKeyFilepath,
                    'failsafe' => [
                        KeyLoaderInterface::TYPE_PUBLIC => $publicKeyFilepath,
                        KeyLoaderInterface::TYPE_PRIVATE => $privateKeyFilepath,
                    ],
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PRIVATE)
            ->shouldReturn(file_get_contents(__DIR__ . '/../..' . $privateKeyFilepath));
    }

    public function it_can_get_user_private_key(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => '/../../../tests/src/Resources/keys/user/public.key',
                    KeyLoaderInterface::TYPE_PRIVATE => '/../../../tests/src/Resources/keys/user/private.key',
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->getSigningKey()
            ->shouldReturn('/../../../tests/src/Resources/keys/user/private.key');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PRIVATE)
            ->shouldReturn(file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/private.key'));
    }

    public function it_can_get_user_public_key(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => '/../../../tests/src/Resources/keys/user/public.key',
                    KeyLoaderInterface::TYPE_PRIVATE => '/../../../tests/src/Resources/keys/user/private.key',
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->getPublicKey()
            ->shouldReturn('/../../../tests/src/Resources/keys/user/public.key');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn(file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiGwKeyLoader::class);
    }

    public function it_make_sure_that_official_keys_cannot_be_overriden(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        ResponseInterface $response
    ) {
        $configuration = [
            'defaults' => [
                'env' => 'production',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => __DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key',
                    KeyLoaderInterface::TYPE_PRIVATE => __DIR__ . '/../../../../../tests/src/Resources/keys/user/private.key',
                ],
            ],
        ];

        $projectDir = __DIR__;

        $response
            ->getStatusCode()
            ->willReturn(200);

        $jwksArray = json_decode(file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/jwks.json'), true);
        $response
            ->toArray()
            ->willReturn($jwksArray);

        $httpClient
            ->sendRequest(Argument::type(RequestInterface::class))
            ->willReturn($response);

        $key = file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/public.key');
        $keyConverter
            ->fromJWKStoPEMS($jwksArray['keys'])
            ->willReturn([
                $key,
            ]);

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->getPublicKey()
            ->shouldReturn('https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn($key);
    }

    public function it_throws_an_exception_when_failsafe_key_is_empty(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $publicKeyFilepath = '/../../../tests/src/Resources/keys/user/public.jwks.empty';
        $privateKeyFilepath = '/../../../tests/src/Resources/keys/user/private.jwks.json';
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => 'http://a.b.c.d.e.f',
                    KeyLoaderInterface::TYPE_PRIVATE => 'http://a.b.c.d.e.f',
                    'failsafe' => [
                        KeyLoaderInterface::TYPE_PUBLIC => $publicKeyFilepath,
                        KeyLoaderInterface::TYPE_PRIVATE => $privateKeyFilepath,
                    ],
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->shouldThrow(new ApiGwAuthenticationException('Invalid JWKS format of public key at /../../../tests/src/Resources/keys/user/public.jwks.empty, keys array is empty.'))
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function it_throws_an_exception_when_failsafe_key_is_invalid(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $publicKeyFilepath = '/../../../tests/src/Resources/keys/user/public.jwks.invalid';
        $privateKeyFilepath = '/../../../tests/src/Resources/keys/user/private.jwks.json';
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => 'http://a.b.c.d.e.f',
                    KeyLoaderInterface::TYPE_PRIVATE => 'http://a.b.c.d.e.f',
                    'failsafe' => [
                        KeyLoaderInterface::TYPE_PUBLIC => $publicKeyFilepath,
                        KeyLoaderInterface::TYPE_PRIVATE => $privateKeyFilepath,
                    ],
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);

        $this
            ->shouldThrow(new ApiGwAuthenticationException('Invalid JWKS format of public key at /../../../tests/src/Resources/keys/user/public.jwks.invalid.'))
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function let(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter
    ) {
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => '/../../../tests/src/Resources/user/public.key',
                    KeyLoaderInterface::TYPE_PRIVATE => '/../../../tests/src/Resources/user/private.key',
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $requestFactory, $keyConverter, $projectDir, $configuration);
    }
}
