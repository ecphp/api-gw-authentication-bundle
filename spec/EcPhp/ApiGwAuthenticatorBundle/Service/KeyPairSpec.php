<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Service;

use EcPhp\ApiGwAuthenticatorBundle\Service\Key;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyPair;
use PhpSpec\ObjectBehavior;

class KeyPairSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(KeyPair::class);
    }

    public function let()
    {
        $this->beConstructedWith(new Key(''), new Key(''));
    }
}
