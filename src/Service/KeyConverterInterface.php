<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

interface KeyConverterInterface
{
    public function toPEM(array $jwk): string;
}
