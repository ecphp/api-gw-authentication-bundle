<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Security;

use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserInterface;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProviderInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use Firebase\JWT\ExpiredException;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiGwAuthenticatorGuardSpec extends ObjectBehavior
{
    public function it_can_check_if_a_request_supports_authentication(ApiGwManagerInterface $apiGwManager, Request $request, HeaderBag $headerBag)
    {
        $this->beConstructedWith($apiGwManager);

        $request->headers = $headerBag;

        $headerBag
            ->get('authorization')
            ->willReturn('Bearer token');

        $this
            ->supports($request)
            ->shouldReturn(true);

        $headerBag
            ->get('authorization')
            ->willReturn('Bearer foo bar');

        $this
            ->supports($request)
            ->shouldReturn(true);

        $headerBag
            ->get('authorization')
            ->willReturn('Bearer');

        $this
            ->supports($request)
            ->shouldReturn(false);

        $headerBag
            ->get('authorization')
            ->willReturn('');

        $this
            ->supports($request)
            ->shouldReturn(false);
    }

    public function it_can_checkCredentials(UserInterface $user)
    {
        $username = 'foo@bar.com';

        $credentials = [
            'sub' => $username,
        ];

        $user
            ->getUsername()
            ->willReturn($username);

        $this
            ->checkCredentials($credentials, $user)
            ->shouldReturn(true);
    }

    public function it_can_create_a_user_from_valid_credentials(ApiGwManagerInterface $apiGwManager, ApiGwAuthenticatorUserProviderInterface $userProvider, ApiGwAuthenticatorUserInterface $user)
    {
        $this->beConstructedWith($apiGwManager);

        $username = 'foo@bar.com';
        $credentials = [
            'sub' => $username,
        ];

        $userProvider
            ->loadUserByPayload($credentials)
            ->willReturn($user);

        $user
            ->getUsername()
            ->willReturn($username);

        $user = $this
            ->getUser($credentials, $userProvider);

        $user
            ->shouldBeAnInstanceOf(ApiGwAuthenticatorUserInterface::class);

        $user
            ->getUsername()
            ->shouldReturn($username);
    }

    public function it_can_getCredentials_with_a_valid_token(ApiGwManagerInterface $apiGwManager, Request $request, HeaderBag $headerBag)
    {
        $credentials = [
            'sub' => 'foo@bar.com',
        ];

        $apiGwManager
            ->decode('ValidToken')
            ->willReturn($credentials);

        $this->beConstructedWith($apiGwManager);

        $headerBag
            ->get('authorization')
            ->willReturn('Bearer ValidToken');

        $request->headers = $headerBag;

        $this
            ->getCredentials($request)
            ->shouldReturn($credentials);
    }

    public function it_can_throw_if_userprovider_is_invalid(ApiGwManagerInterface $apiGwManager, InMemoryUserProvider $userProvider, ApiGwAuthenticatorUserInterface $user)
    {
        $this->beConstructedWith($apiGwManager);

        $username = 'foo@bar.com';
        $credentials = [
            'sub' => $username,
        ];

        $this
            ->shouldThrow(AuthenticationException::class)
            ->during('getUser', [$credentials, $userProvider]);
    }

    public function it_cannot_getCredentials_with_a_invalid_token(ApiGwManagerInterface $apiGwManager, Request $request, HeaderBag $headerBag)
    {
        $apiGwManager
            ->decode('InvalidToken')
            ->willThrow(ExpiredException::class);

        $this->beConstructedWith($apiGwManager);

        $headerBag
            ->get('authorization')
            ->willReturn('Bearer InvalidToken');

        $request->headers = $headerBag;

        $this
            ->shouldThrow(ExpiredException::class)
            ->during('getCredentials', [$request]);
    }

    public function let(ApiGwManagerInterface $apiGwManagerInterface)
    {
        $this
            ->beConstructedWith($apiGwManagerInterface);
    }
}
