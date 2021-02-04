<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Security\Core\User;

use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserInterface;
use PhpSpec\ObjectBehavior;

class ApiGwAuthenticatorUserSpec extends ObjectBehavior
{
    public function it_can_get_its_username()
    {
        $this
            ->getUsername()
            ->shouldBeString();

        $this
            ->getUsername()
            ->shouldBeEqualTo($this->getUsername());
    }

    public function it_can_get_one_attribute()
    {
        $this
            ->getAttribute('foo')
            ->shouldReturn('bar');

        $this
            ->getAttribute('unknownAttribute')
            ->shouldReturn(null);

        $this
            ->getAttribute('unknownAttribute', 'foo')
            ->shouldReturn('foo');
    }

    public function it_can_get_roles()
    {
        $this
            ->getRoles()
            ->shouldReturn(['IS_AUTHENTICATED_FULLY']);
    }

    public function it_can_get_the_attributes()
    {
        $this
            ->getAttributes()
            ->shouldReturn([
                'sub' => 'sub',
                'foo' => 'bar',
            ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiGwAuthenticatorUserInterface::class);
    }

    public function let()
    {
        $credentials = [
            'sub' => 'sub',
            'foo' => 'bar',
        ];

        $this
            ->beConstructedWith($credentials);
    }
}
