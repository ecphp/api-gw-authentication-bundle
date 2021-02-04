<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

interface ApiGwManagerInterface
{
    public function decode(string $jwt): array;

    public function encode(array $payload): string;
}
