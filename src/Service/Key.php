<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

final class Key implements KeyInterface
{
    private ?string $key;

    public function __construct(?string $key)
    {
        $this->key = $key;
    }

    public function __toString(): string
    {
        return str_replace("\r\n", "\n", (string) $this->key);
    }
}
