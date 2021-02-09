<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Security\Core\User;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ApiGwAuthenticationUserProvider implements PayloadAwareUserProviderInterface
{
    private PayloadAwareUserProviderInterface $userProvider;

    public function __construct()
    {
        $this->userProvider = new JWTUserProvider(ApiGwAuthenticationUser::class);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->userProvider->loadUserByUsername($username);
    }

    public function loadUserByUsernameAndPayload($username, array $payload)
    {
        return $this->userProvider->loadUserByUsernameAndPayload($username, $payload);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->userProvider->refreshUser($user);
    }

    public function supportsClass(string $class): bool
    {
        return $this->userProvider->supportsClass($class);
    }
}
