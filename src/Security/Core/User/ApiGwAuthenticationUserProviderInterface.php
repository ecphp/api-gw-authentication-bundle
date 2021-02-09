<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Security\Core\User;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;

interface ApiGwAuthenticationUserProviderInterface extends PayloadAwareUserProviderInterface
{
}
