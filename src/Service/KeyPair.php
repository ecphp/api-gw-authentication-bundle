<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Service;

final class KeyPair implements KeyPairInterface
{
    private KeyConverterInterface $keyConverter;

    private ?array $private;

    private array $public;

    public function __construct(KeyConverterInterface $keyConverter, array $public, ?array $private)
    {
        $this->keyConverter = $keyConverter;
        $this->public = $public;
        $this->private = $private;
    }

    public function getPrivate(): KeyInterface
    {
        return new Key($this->keyConverter, $this->private);
    }

    public function getPublic(): KeyInterface
    {
        return new Key($this->keyConverter, $this->public);
    }
}
