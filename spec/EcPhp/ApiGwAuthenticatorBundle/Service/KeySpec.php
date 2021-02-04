<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Service;

use EcPhp\ApiGwAuthenticatorBundle\Service\Key;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverterInterface;
use PhpSpec\ObjectBehavior;

class KeySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Key::class);
    }

    public function let(KeyConverterInterface $keyConverter)
    {
        $this->beConstructedWith($keyConverter, []);
    }
}
