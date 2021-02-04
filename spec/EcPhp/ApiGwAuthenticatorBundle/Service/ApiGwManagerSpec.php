<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Service;

use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwKeyManagerInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyPairInterface;
use Firebase\JWT\JWT;
use PhpSpec\ObjectBehavior;

class ApiGwManagerSpec extends ObjectBehavior
{
    public function it_can_decode_a_jwt_token(ApiGwKeyManagerInterface $apiGwKeyManager, KeyPairInterface $keyPair, KeyInterface $public, KeyInterface $private)
    {
        $configuration = [
            'defaults' => [
                'env' => 'foo',
            ],
            'envs' => [
                'foo' => [
                    'public' => 'https://foobarfoooooooo.bar',
                    'private' => 'failsafe',
                ],
            ],
        ];

        $jwt = new JWT();

        $apiGwKeyManager
            ->getKeyPair('foo')
            ->willReturn($keyPair);

        $keyPair
            ->getPublic()
            ->willReturn($public);

        $public
            ->getPEM()
            ->willReturn(file_get_contents(__DIR__ . '/../../../../tests/src/Resources/keys/foo/public.key'));

        $private
            ->getPEM()
            ->willReturn(file_get_contents(__DIR__ . '/../../../../tests/src/Resources/keys/foo/private.key'));

        $keyPair
            ->getPrivate()
            ->willReturn($private);

        $this->beConstructedWith($jwt, $apiGwKeyManager, $configuration);

        $token = $jwt::encode(
            [
                'sub' => 'sub',
            ],
            file_get_contents(__DIR__ . '/../../../../tests/src/Resources/keys/foo/private.key'),
            'RS256'
        );

        $this
            ->decode($token)
            ->shouldReturn([
                'sub' => 'sub',
            ]);
    }
}
