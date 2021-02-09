<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Security\Core\User;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

interface ApiGwAuthenticationUserInterface extends JWTUserInterface
{
    /**
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute(string $key, $default = null);

    /**
     * @return array<array|string>
     */
    public function getAttributes(): array;
}
