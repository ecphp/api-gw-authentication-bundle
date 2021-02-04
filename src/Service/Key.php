<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

use CoderCat\JWKToPEM\JWKConverter;

final class Key
{
    private array $jwk;

    private JWKConverter $jwkConverter;

    public function __construct(JWKConverter $jwkConverter, array $jwk)
    {
        $this->jwk = $jwk;
        $this->jwkConverter = $jwkConverter;
    }

    public function getJWK(): array
    {
        return $this->jwk;
    }

    public function getPEM(): string
    {
        return $this->jwkConverter->toPEM($this->jwk);
    }
}
