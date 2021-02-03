<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Security;

use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProviderInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiGwGuardAuthenticator extends AbstractGuardAuthenticator
{
    private ApiGwManagerInterface $apiGwManager;

    public function __construct(ApiGwManagerInterface $apiGwManager)
    {
        $this->apiGwManager = $apiGwManager;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $user->getUsername() === $credentials['sub'];
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request): array
    {
        [,$token] = explode('Bearer ', $request->headers->get('authorization'), 2);

        return $this->apiGwManager->decode($token);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
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

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            [
                'error' => $exception->getMessageKey(),
            ],
            400
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // Obsolete
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        return new JsonResponse(['Access Denied'], 403);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        if (null === $header = $request->headers->get('Authorization')) {
            return false;
        }

        return 0 === mb_strpos($header, 'Bearer ', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
