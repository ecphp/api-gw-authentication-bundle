<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter;

interface KeyConverterInterface
{
    public function fromJWKStoPEMS(array $jwks): array;

    public function fromJWKtoPEM(array $jwk): string;
}
