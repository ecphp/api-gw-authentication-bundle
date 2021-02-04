<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

use CoderCat\JWKToPEM\JWKConverter;

final class KeyPair
{
    private JWKConverter $jwkConverter;

    private ?array $private;

    private array $public;

    public function __construct(JWKConverter $jwkConverter, array $public, ?array $private)
    {
        $this->jwkConverter = $jwkConverter;
        $this->public = $public;
        $this->private = $private;
    }

    public function getPrivate(): Key
    {
        return new Key($this->jwkConverter, current($this->private['keys']));
    }

    public function getPublic(): Key
    {
        return new Key($this->jwkConverter, current($this->public['keys']));
    }
}
