<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EcPhp\ApiGwAuthenticatorBundle\Controller\Token;
use EcPhp\ApiGwAuthenticatorBundle\Controller\User;
use EcPhp\ApiGwAuthenticatorBundle\Security\ApiGwAuthenticatorGuard;
use EcPhp\ApiGwAuthenticatorBundle\Security\Auth\ApiGwAuthenticatorFailure;
use EcPhp\ApiGwAuthenticatorBundle\Security\Auth\ApiGwAuthenticatorSuccess;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProvider;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManager;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

return static function (ContainerConfigurator $container) {
    $container
        ->services()
        ->set('apigwauthenticator.auth.successhandler', ApiGwAuthenticatorSuccess::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwauthenticator.auth.failurehandler', ApiGwAuthenticatorFailure::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwauthenticator.manager', ApiGwManager::class)
        ->args([
            service('http_client'),
            service('apigwauthenticator.jwt'),
            service('apigwauthenticator.jwk'),
            '%api_gw_authenticator%',
            '%kernel.project_dir%',
        ])
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->alias(ApiGwManagerInterface::class, 'apigwauthenticator.manager');

    $container
        ->services()
        ->set('apigwauthenticator.userprovider', ApiGwAuthenticatorUserProvider::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwauthenticator.jwt', JWT::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->alias(JWT::class, 'apigwauthenticator.jwt');

    $container
        ->services()
        ->set('apigwauthenticator.jwk', JWK::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->alias(JWK::class, 'apigwauthenticator.jwk');

    $container
        ->services()
        ->set('apigwauthenticator.guardauthenticator', ApiGwAuthenticatorGuard::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set(User::class)
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');

    $container
        ->services()
        ->set(Token::class)
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');
};
