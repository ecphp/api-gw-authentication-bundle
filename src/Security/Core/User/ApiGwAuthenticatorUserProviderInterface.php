<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Security\Core\User;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;

interface ApiGwAuthenticatorUserProviderInterface extends PayloadAwareUserProviderInterface
{
}
