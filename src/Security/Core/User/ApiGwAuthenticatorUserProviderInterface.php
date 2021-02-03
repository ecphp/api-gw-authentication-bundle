<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Security\Core\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

interface ApiGwAuthenticatorUserProviderInterface extends UserProviderInterface
{
    public function loadUserByPayload(array $data): ApiGwAuthenticatorUserInterface;
}
