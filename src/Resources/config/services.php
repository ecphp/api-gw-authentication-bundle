<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CoderCat\JWKToPEM\JWKConverter;
use EcPhp\ApiGwAuthenticatorBundle\Controller\User;
use EcPhp\ApiGwAuthenticatorBundle\Security\ApiGwAuthenticatorGuard;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProvider;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwKeyManager;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManager;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use Firebase\JWT\JWT;

return static function (ContainerConfigurator $container) {
    $container
        ->services()
        ->set('apigwkeymanager', ApiGwKeyManager::class)
        ->args([
            service('http_client'),
            service('apigwmanager.jwkconverter'),
            '%api_gw_authenticator%',
        ])
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwmanager', ApiGwManager::class)
        ->args([
            service('apigwmanager.jwt'),
            service('apigwkeymanager'),
            '%api_gw_authenticator%',
        ])
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwauthenticator.userprovider', ApiGwAuthenticatorUserProvider::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwmanager.jwt', JWT::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwmanager.jwkconverter', JWKConverter::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->alias(ApiGwManagerInterface::class, 'apigwmanager');

    $container
        ->services()
        ->alias(JWT::class, 'apigwmanager.jwt');

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
};
