<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Security\Core\User;

use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUser;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserInterface;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProvider;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\User;

class ApiGwAuthenticatorUserProviderSpec extends ObjectBehavior
{
    public function it_can_check_if_it_is_possible_to_load_a_user_by_username()
    {
        $this
            ->loadUserByUsername('foo')
            ->shouldReturnAnInstanceOf(ApiGwAuthenticatorUserInterface::class);
    }

    public function it_can_check_if_the_user_class_is_supported()
    {
        $this
            ->supportsClass(ApiGwAuthenticatorUser::class)
            ->shouldReturn(true);

        $this
            ->supportsClass(User::class)
            ->shouldReturn(false);
    }

    public function it_can_load_by_username_and_payload()
    {
        $this
            ->loadUserByUsernameAndPayload('foo', ['sub', 'sub'])
            ->shouldReturnAnInstanceOf(ApiGwAuthenticatorUserInterface::class);
    }

    public function it_can_refresh_the_user()
    {
        $user = new ApiGwAuthenticatorUser(uniqid());

        $this
            ->refreshUser($user)
            ->shouldReturn($user);

        $user = new User('username', 'password');

        $this
            ->refreshUser($user)
            ->shouldReturn($user);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiGwAuthenticatorUserProvider::class);
    }
}
