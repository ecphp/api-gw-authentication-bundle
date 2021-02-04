<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Service;

use CoderCat\JWKToPEM\JWKConverter;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverter;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverterInterface;
use Firebase\JWT\JWT;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiGwManagerSpec extends ObjectBehavior
{
    public function it_can_decode_a_jwt_token()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJzdWIifQ.X2KATFF3XWDd45pDTGZt0BcAl5z5EVvbfyivPBgDS87lt20BwhywXB6jAjI76gVaIdEzN2TJY18GDAyvJqEBmkC29dCKb2sB4aGHba83ZQnDE89Ad3McfEgqMwxfxLJ57KCmohV-PJFgC-rKQi9aAJT7orm0ZL_gGSAWWAMTJTO7K3y2ruCFcv3Q3rDmoxP-ataNBm_yZfhyfcHRI30LzQ_O6-rPAi5F7dxr3E3_pAEKLmjbiPC9HsNu2M0YN823dHNWgwrwvGI4-bpL8nb334ojx6Z5cuPT57cw-e3_IMyYYhR45WJ4LEYfU9hYlGqNIx2n7ozvUieFtM57ZkpXcuLAnQ7g7NK-7Tu5m5e3lSOl18TkL_BwCUNFYUjBOt5XmwSy5N7gjREX9Xxnxrl0xUoMDiy8ywh_GVmxTIJZ78ndIa5XVUnZ2C5q5H5xYVFgPKwaRH9a4gtcl7mV9vkyV8kGlkhTLGaHmBeWYeM9uykdikNQ0nwzQzp6sVtj9TgmPVi8DQPHvDIcd83pMq1pGbgXbWFBMsrMeCubt9ySW_DiygQDELJq9OrKs_l7Uz1OxnUQP3dM2RZVHsOzQpfO2qItbrsWr7U3LJURjZz9IVty1H1DlrcVcbH4U4yOhMMH1CWG3WqvsR1Yi1FOQ6jILlKUaHNOSBfgbYWECXt78Ww';

        $configuration = [
            'defaults' => [
                'env' => 'foo',
            ],
            'envs' => [
                'foo' => [
                    'public' => __DIR__ . '/../../../../tests/src/Resources/keys/foo/public.key',
                    'private' => __DIR__ . '/../../../../tests/src/Resources/keys/foo/private.key',
                ],
            ],
        ];

        $this->beConstructedWith(
            HttpClient::create(),
            new JWT(),
            new KeyConverter(new JWKConverter()),
            $configuration,
            '.'
        );

        $this
            ->decode($token)
            ->shouldReturn([
                'sub' => 'sub',
            ]);
    }

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

        $this->beConstructedWith(
            $httpClient,
            new JWT(),
            $keyConverter,
            $configuration,
            __DIR__ . '/../../../../',
        );

        $this
            ->getKeyPair('production')
            ->getPublic()
            ->__toString()
            ->shouldReturn(
                file_get_contents(__DIR__ . '/../../../../src/Resources/keys/production/public.key')
            );
    }

    public function it_can_get_public_key_from_http_call(HttpClientInterface $httpClient, KeyConverterInterface $keyConverter)
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

        $this->beConstructedWith(
            $httpClient,
            new JWT(),
            $keyConverter,
            $configuration,
            __DIR__ . '/../../../../'
        );

        $this
            ->getKeyPair('production')
            ->getPublic()
            ->__toString()
            ->shouldReturn(
                file_get_contents(__DIR__ . '/../../../../src/Resources/keys/production/public.key')
            );
    }
}
