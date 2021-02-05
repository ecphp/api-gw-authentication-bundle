<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Security;

use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProviderInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Throwable;

class ApiGwAuthenticatorGuard extends AbstractGuardAuthenticator
{
    private ApiGwManagerInterface $apiGwManager;

    public function __construct(ApiGwManagerInterface $apiGwManager)
    {
        $this->apiGwManager = $apiGwManager;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function getCredentials(Request $request): string
    {
        return mb_substr($request->headers->get('authorization'), 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        try {
            $credentials = $this->apiGwManager->decode($credentials);
        } catch (Throwable $e) {
            throw new AuthenticationException('Unable to load the user through the given token.');
        }

        if (false === ($userProvider instanceof ApiGwAuthenticatorUserProviderInterface)) {
            throw new AuthenticationException('Unable to load the user through the given User Provider.');
        }

        try {
            $user = $userProvider->loadUserByPayload($credentials);
        } catch (AuthenticationException $exception) {
            throw $exception;
        }

        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        throw new HttpException(400, $exception->getMessage(), $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
    }

    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        return new JsonResponse(['error' => 'Access denied'], 403);
        // Todo: Should we throw here?
        //throw new AccessDeniedHttpException('Access denied', $authException);
    }

    public function supports(Request $request): bool
    {
        if (false === $request->headers->has('authorization')) {
            return false;
        }

        $authorization = $request->headers->get('authorization');

        if (0 !== mb_strpos($authorization, 'Bearer ', 0)) {
            return false;
        }

        return '' !== mb_substr($authorization, 7);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
