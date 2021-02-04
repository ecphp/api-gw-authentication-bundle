<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

interface KeyPairInterface
{
    public function getPrivate(): KeyInterface;

    public function getPublic(): KeyInterface;
}
