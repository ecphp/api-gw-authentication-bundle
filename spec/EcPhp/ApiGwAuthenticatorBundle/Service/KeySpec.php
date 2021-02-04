<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Service;

use EcPhp\ApiGwAuthenticatorBundle\Service\Key;
use PhpSpec\ObjectBehavior;

class KeySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Key::class);
    }

    public function let()
    {
        $this->beConstructedWith('');
    }
}
