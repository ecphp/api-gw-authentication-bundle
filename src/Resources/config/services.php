<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CoderCat\JWKToPEM\JWKConverter;
use EcPhp\ApiGwAuthenticatorBundle\Controller\Token;
use EcPhp\ApiGwAuthenticatorBundle\Controller\User;
use EcPhp\ApiGwAuthenticatorBundle\Security\Core\User\ApiGwAuthenticatorUserProvider;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverter\KeyConverter;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverter\KeyConverterInterface;
use EcPhp\ApiGwAuthenticatorBundle\Service\Keyloader\ApiGwKeyLoader;

return static function (ContainerConfigurator $container) {
    $container
        ->services()
        ->set('apigwauthenticator.key_converter.jwk_converter', JWKConverter::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwauthenticator.key_converter', KeyConverter::class)
        ->arg('$jwkConverter', service('apigwauthenticator.key_converter.jwk_converter'))
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->alias(
            KeyConverterInterface::class,
            'apigwauthenticator.key_converter'
        );

    $container
        ->services()
        ->set('apigwauthenticator.api_gw_keyloader', ApiGwKeyLoader::class)
        ->decorate('lexik_jwt_authentication.key_loader.raw')
        ->arg('$configuration', '%api_gw_authenticator%')
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('apigwauthenticator.userprovider', ApiGwAuthenticatorUserProvider::class)
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
