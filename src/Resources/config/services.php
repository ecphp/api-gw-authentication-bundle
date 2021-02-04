<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CoderCat\JWKToPEM\JWKConverter;
use EcPhp\ApiGwAuthenticatorBundle\Controller\Token;
use EcPhp\ApiGwAuthenticatorBundle\Controller\User;
use EcPhp\ApiGwAuthenticatorBundle\Security\ApiGwAuthenticatorGuard;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProvider;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManager;
use EcPhp\ApiGwAuthenticatorBundle\Service\ApiGwManagerInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverter;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverterInterface;
use Firebase\JWT\JWT;

return static function (ContainerConfigurator $container) {
    $container
        ->services()
        ->set('apigwauthenticator.jwkconverter', JWKConverter::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwauthenticator.keyconverter', KeyConverter::class)
        ->args([
            service('apigwauthenticator.jwkconverter'),
        ])
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->alias(KeyConverterInterface::class, 'apigwauthenticator.keyconverter');

    $container
        ->services()
        ->set('apigwauthenticator.manager', ApiGwManager::class)
        ->args([
            service('http_client'),
            service('apigwauthenticator.jwt'),
            service('apigwauthenticator.keyconverter'),
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
