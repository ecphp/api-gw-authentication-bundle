<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

final class Key implements KeyInterface
{
    private array $jwk;

    private KeyConverterInterface $keyConverter;

    public function __construct(KeyConverterInterface $keyConverter, array $jwk)
    {
        $this->jwk = $jwk;
        $this->keyConverter = $keyConverter;
    }

    public function getJWK(): array
    {
        return $this->jwk;
    }

    public function getPEM(): string
    {
        return $this->keyConverter->toPEM($this->jwk);
    }
}
