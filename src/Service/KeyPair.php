<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

final class KeyPair implements KeyPairInterface
{
    private ?KeyInterface $private;

    private KeyInterface $public;

    public function __construct(KeyInterface $public, KeyInterface $private)
    {
        $this->public = $public;
        $this->private = $private;
    }

    public function getPrivate(): KeyInterface
    {
        return $this->private;
    }

    public function getPublic(): KeyInterface
    {
        return $this->public;
    }
}
