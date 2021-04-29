<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter;

use CoderCat\JWKToPEM\JWKConverter;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverter;
use PhpSpec\ObjectBehavior;

class KeyConverterSpec extends ObjectBehavior
{
    public function it_can_convert_from_jwks_to_pems()
    {
        $jwks = file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/public.jwks.json');
        $jwksArray = json_decode($jwks, true);

        $pem = str_replace(
            "\n",
            "\r\n",
            trim(file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key'))
        );

        $this
            ->fromJWKStoPEMS($jwksArray['keys'])
            ->shouldReturn([
                $pem,
            ]);
    }

    public function it_can_convert_from_pems_to_jwks()
    {
        $jwks = file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/public.jwks.json');
        $jwksArray = json_decode($jwks, true);

        $pem = str_replace(
            "\n",
            "\r\n",
            trim(file_get_contents(__DIR__ . '/../../../../../tests/src/Resources/keys/user/public.key'))
        );

        $this
            ->fromJWKtoPEM($jwksArray['keys'][0])
            ->shouldReturn(
                $pem,
            );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(KeyConverter::class);
    }

    public function let()
    {
        $this->beConstructedWith(new JWKConverter());
    }
}
