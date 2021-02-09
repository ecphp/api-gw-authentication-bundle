<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader\ApiGwKeyLoader;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiGwKeyLoaderSpec extends ObjectBehavior
{
    public function it_can_get_the_api_gateway_production_failsafe_key(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, ResponseInterface $response)
    {
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
            ->toArray()
            ->willReturn([]);

        $httpClient
            ->request('GET', 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json')
            ->willReturn($response);

        $key = file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/public.key');
        $jwksArray = json_decode(file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/jwks.json'), true);
        $keyConverter
            ->fromJWKStoPEMS($jwksArray['keys'])
            ->willReturn([
                $key,
            ]);

        $this->beConstructedWith($httpClient, $keyConverter, $projectDir, $configuration);

        $this
            ->getPublicKey()
            ->shouldReturn('https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn($key);
    }

    public function it_can_get_the_api_gateway_production_key(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, ResponseInterface $response)
    {
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
            ->request('GET', 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json')
            ->willReturn($response);

        $key = file_get_contents(__DIR__ . '/../../../../../src/Resources/keys/production/public.key');
        $keyConverter
            ->fromJWKStoPEMS($jwksArray['keys'])
            ->willReturn([
                $key,
            ]);

        $this->beConstructedWith($httpClient, $keyConverter, $projectDir, $configuration);

        $this
            ->getPublicKey()
            ->shouldReturn('https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn($key);
    }

    public function it_can_get_user_failsafe_private_key(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
    {
        $publicKeyFilepath = __DIR__ . '/../../../../../tests/src/Resources/keys/user/public.jwks.json';
        $privateKeyFilepath = __DIR__ . '/../../../../../tests/src/Resources/keys/user/private.jwks.json';
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

        $jwks = json_decode(file_get_contents($privateKeyFilepath), true);

        $keyConverter
            ->fromJWKStoPEMS($jwks['keys'])
            ->willReturn([
                'foo',
            ]);

        $this->beConstructedWith($httpClient, $keyConverter, $projectDir, $configuration);

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PRIVATE)
            ->shouldReturn('foo');
    }

    public function it_can_get_user_local_private_key(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
    {
        $publicKeyFilepath = __DIR__ . '/../../../../../tests/src/Resources/keys/user/public.jwks.json';
        $privateKeyFilepath = __DIR__ . '/../../../../../tests/src/Resources/keys/user/private.jwks.json';
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

        $this->beConstructedWith($httpClient, $keyConverter, $projectDir, $configuration);

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PRIVATE)
            ->shouldReturn(file_get_contents($privateKeyFilepath));
    }

    public function it_can_get_user_private_key(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
    {
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => __DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key',
                    KeyLoaderInterface::TYPE_PRIVATE => __DIR__ . '/../../../../../tests/src/Resources/keys/user/private.key',
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $keyConverter, $projectDir, $configuration);

        $this
            ->getSigningKey()
            ->shouldReturn(__DIR__ . '/../../../../../tests/src/Resources/keys/user/private.key');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PRIVATE)
            ->shouldReturn(file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/private.key'));
    }

    public function it_can_get_user_public_key(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
    {
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => __DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key',
                    KeyLoaderInterface::TYPE_PRIVATE => __DIR__ . '/../../../../../tests/src/Resources/keys/user/private.key',
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $keyConverter, $projectDir, $configuration);

        $this
            ->getPublicKey()
            ->shouldReturn(__DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key');

        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn(file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiGwKeyLoader::class);
    }

    public function let(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
    {
        $configuration = [
            'defaults' => [
                'env' => 'user',
            ],
            'envs' => [
                'user' => [
                    KeyLoaderInterface::TYPE_PUBLIC => __DIR__ . '/../../../../../tests/src/Resources/user/public.key',
                    KeyLoaderInterface::TYPE_PRIVATE => __DIR__ . '/../../../../../tests/src/Resources/user/private.key',
                ],
            ],
        ];

        $projectDir = __DIR__;

        $this->beConstructedWith($httpClient, $keyConverter, $projectDir, $configuration);
    }
}
