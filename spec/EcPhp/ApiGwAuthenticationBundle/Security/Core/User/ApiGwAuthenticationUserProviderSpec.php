<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticationBundle\Security\Core\User;

use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUser;
use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUserInterface;
use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUserProvider;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\User;

class ApiGwAuthenticationUserProviderSpec extends ObjectBehavior
{
    public function it_can_check_if_it_is_possible_to_load_a_user_by_username()
    {
        $this
            ->loadUserByUsername('foo')
            ->shouldReturnAnInstanceOf(ApiGwAuthenticationUserInterface::class);
    }

    public function it_can_check_if_the_user_class_is_supported()
    {
        $this
            ->supportsClass(ApiGwAuthenticationUser::class)
            ->shouldReturn(true);

        $this
            ->supportsClass(User::class)
            ->shouldReturn(false);
    }

    public function it_can_load_by_username_and_payload()
    {
        $this
            ->loadUserByUsernameAndPayload('foo', ['sub', 'sub'])
            ->shouldReturnAnInstanceOf(ApiGwAuthenticationUserInterface::class);
    }

    public function it_can_refresh_the_user()
    {
        $user = new ApiGwAuthenticationUser(uniqid());

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
        $this->shouldHaveType(ApiGwAuthenticationUserProvider::class);
    }
}
