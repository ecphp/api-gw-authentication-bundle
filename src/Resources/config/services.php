<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EcPhp\ApiGwAuthenticatorBundle\Controller\User;
use EcPhp\ApiGwAuthenticatorBundle\Security\ApiGwGuardAuthenticator;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProvider;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManager;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use Firebase\JWT\JWT;

return static function (ContainerConfigurator $container) {
    $container
        ->services()
        ->set('apigwauthenticator.userprovider', ApiGwAuthenticatorUserProvider::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwmanager', ApiGwManager::class)
        ->args([
            service('apigwmanager.jwt'),
            '%api_gw_authenticator%',
        ])
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwmanager.jwt', JWT::class)
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
        ->set('apigwauthenticator.guardauthenticator', ApiGwGuardAuthenticator::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set(User::class)
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');
};
