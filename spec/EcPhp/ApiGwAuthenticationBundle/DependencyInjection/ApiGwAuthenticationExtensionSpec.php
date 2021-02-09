<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticationBundle\DependencyInjection;

use EcPhp\ApiGwAuthenticationBundle\DependencyInjection\ApiGwAuthenticationExtension;
use PhpSpec\ObjectBehavior;

class ApiGwAuthenticationExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiGwAuthenticationExtension::class);
    }
}
