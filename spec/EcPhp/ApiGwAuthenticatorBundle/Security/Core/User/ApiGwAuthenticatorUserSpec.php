<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticationBundle\Security\Core\User;

use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUserInterface;
use PhpSpec\ObjectBehavior;

class ApiGwAuthenticationUserSpec extends ObjectBehavior
{
    public function it_can_be_created_through_createPayload()
    {
        $subject = $this::createFromPayload('username', ['sub' => 'sub']);

        $subject
            ->shouldBeAnInstanceOf(ApiGwAuthenticationUserInterface::class);

        $subject
            ->getUsername()
            ->shouldReturn('username');

        $subject
            ->get('sub')
            ->shouldReturn('sub');
    }

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
        $this->shouldHaveType(ApiGwAuthenticationUserInterface::class);

        $this
            ->eraseCredentials()
            ->shouldReturn(null);

        $this
            ->getPassword()
            ->shouldReturn(null);

        $this
            ->getSalt()
            ->shouldReturn(null);
    }

    public function let()
    {
        $credentials = [
            'sub' => 'sub',
            'foo' => 'bar',
        ];

        $this
            ->beConstructedWith('username', $credentials);
    }
}
