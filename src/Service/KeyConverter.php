<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

use CoderCat\JWKToPEM\JWKConverter;

final class KeyConverter implements KeyConverterInterface
{
    private JWKConverter $jwkConverter;

    public function __construct(JWKConverter $jwkConverter)
    {
        $this->jwkConverter = $jwkConverter;
    }

    public function toJWK(string $pem): array
    {
        $keyInfo = openssl_pkey_get_details(openssl_pkey_get_public($pem));

        return [
            'keys' => [
                [
                    'kty' => 'RSA',
                    'n' => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['n'])), '='),
                    'e' => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['e'])), '='),
                ],
            ],
        ];
    }

    public function toPEM(array $jwk): string
    {
        return $this->jwkConverter->toPEM(current($jwk['keys']));
    }
}
