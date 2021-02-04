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

    public function toPEM(array $jwk): string
    {
        return $this->jwkConverter->toPEM(current($jwk['keys']));
    }
}
