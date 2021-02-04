<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

interface ApiGwKeyManagerInterface
{
    public function getKeyPair(string $env): KeyPairInterface;
}
