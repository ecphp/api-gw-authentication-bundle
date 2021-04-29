<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use EcPhp\ApiGwAuthenticationBundle\Controller\Token;
use EcPhp\ApiGwAuthenticationBundle\Controller\User;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $routes
        ->add('api_gw_authentication_bundle_user', '/user')
        ->controller(User::class);

    $routes
        ->add('api_gw_authentication_bundle_token', '/token')
        ->controller(Token::class);
};
