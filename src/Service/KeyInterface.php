<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

interface KeyInterface
{
    public function getJWK(): array;

    public function getPEM(): string;
}
