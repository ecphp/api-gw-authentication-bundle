<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\DependencyInjection;

use EcPhp\ApiGwAuthenticatorBundle\DependencyInjection\ApiGwAuthenticatorExtension;
use PhpSpec\ObjectBehavior;

class ApiGwAuthenticatorExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiGwAuthenticatorExtension::class);
    }
}
