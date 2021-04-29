<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CoderCat\JWKToPEM\JWKConverter;
use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUserProvider;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverter;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader\ApiGwKeyLoader;
use Symfony\Component\HttpClient\CachingHttpClient;

return static function (ContainerConfigurator $container) {
    $container
        ->services()
        ->set('api_gw_authentication.http_client', CachingHttpClient::class)
        ->arg('$store', service('http_cache.store'))
        ->autowire(true)
        ->autoconfigure(true);
    $container
        ->services()
        ->set('api_gw_authentication.key_converter.jwk_converter', JWKConverter::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('api_gw_authentication.key_converter', KeyConverter::class)
        ->arg('$jwkConverter', service('api_gw_authentication.key_converter.jwk_converter'))
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->alias(
            KeyConverterInterface::class,
            'api_gw_authentication.key_converter'
        );

    $container
        ->services()
        ->set('api_gw_authentication.api_gw_keyloader', ApiGwKeyLoader::class)
        ->decorate('lexik_jwt_authentication.key_loader.raw')
        ->arg('$configuration', '%api_gw_authentication%')
        ->arg('$projectDir', '%kernel.project_dir%')
        ->arg('$httpClient', service('api_gw_authentication.http_client'))
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set('api_gw_authentication.user_provider', ApiGwAuthenticationUserProvider::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->load('EcPhp\\ApiGwAuthenticationBundle\\Controller\\', __DIR__ . '/../../Controller')
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');
};
