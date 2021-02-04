<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Service;

use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwKeyManager;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverterInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiGwKeyManagerSpec extends ObjectBehavior
{
    public function it_can_get_public_key_from_failsafe(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
    {
        $configuration = [
            'defaults' => [
                'env' => 'production',
            ],
            'envs' => [
                'production' => [
                    'public' => 'https://' . uniqid() . '-' . uniqid() . '.com', // Unexistant URL
                    'private' => null,
                ],
            ],
        ];

        $this->beConstructedWith($httpClient, $keyConverter, $configuration);

        $this
            ->getKeyPair('production')
            ->getPublic()
            ->getJWK()
            ->shouldReturn([
                'keys' => [
                    [
                        'kty' => 'RSA',
                        'e' => 'AQAB',
                        'use' => 'sig',
                        'kid' => 'eucommission',
                        'alg' => 'RS256',
                        'n' => 'w812qiypKhkfHt1BXtxSSVSCWb2iaz0cZ2JBQlqYy819sQtLtNL5O7S0vH9KzyDiHbY0VjwPA8LneEtR9-KWcfgWjgpOhzLiOzZVQ9WKRDbD5jWOF6Ei9OLoRnS9iN4_AFdqW4P1fZ6O_I3LANsLk-GvZuiDE-aEfDrNcv6UkpfxwIDLVlFlw8hPusbr2i8V_ufeczxcmSK_kB2nHJ6fY385cC8uYBLmxT7GGS6ytm8tUj-Xdd9x0NGtzWlvxVi5A3GLTT4Ryv7pRO7bPrWU-uISSXDCf6OzhV8H328AW9fZouiG1NGR9UleJU4vDlnBTI6hMMTUw3k-fhPujr2VUQ',
                    ],
                ],
            ]);
    }

    public function it_can_get_public_key_from_remote()
    {
        $this
            ->getKeyPair('production')
            ->getPublic()
            ->getJWK()
            ->shouldReturn([
                'keys' => [
                    [
                        'kty' => 'RSA',
                        'e' => 'AQAB',
                        'use' => 'sig',
                        'kid' => 'eucommission',
                        'alg' => 'RS256',
                        'n' => 'w812qiypKhkfHt1BXtxSSVSCWb2iaz0cZ2JBQlqYy819sQtLtNL5O7S0vH9KzyDiHbY0VjwPA8LneEtR9-KWcfgWjgpOhzLiOzZVQ9WKRDbD5jWOF6Ei9OLoRnS9iN4_AFdqW4P1fZ6O_I3LANsLk-GvZuiDE-aEfDrNcv6UkpfxwIDLVlFlw8hPusbr2i8V_ufeczxcmSK_kB2nHJ6fY385cC8uYBLmxT7GGS6ytm8tUj-Xdd9x0NGtzWlvxVi5A3GLTT4Ryv7pRO7bPrWU-uISSXDCf6OzhV8H328AW9fZouiG1NGR9UleJU4vDlnBTI6hMMTUw3k-fhPujr2VUQ',
                    ],
                ],
            ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiGwKeyManager::class);
    }

    public function let(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
    {
        $configuration = [
            'defaults' => [
                'env' => 'production',
            ],
            'envs' => [
                'production' => [
                    'public' => 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
                    'private' => null,
                ],
            ],
        ];

        $this->beConstructedWith($httpClient, $keyConverter, $configuration);
    }
}
