<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

interface KeyConverterInterface
{
    public function toJWK(string $pem): array;

    public function toPEM(array $jwk): string;
}
