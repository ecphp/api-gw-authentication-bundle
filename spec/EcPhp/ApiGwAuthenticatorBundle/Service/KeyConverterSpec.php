<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticatorBundle\Service;

use CoderCat\JWKToPEM\JWKConverter;
use EcPhp\ApiGwAuthenticatorBundle\Service\KeyConverter;
use PhpSpec\ObjectBehavior;

class KeyConverterSpec extends ObjectBehavior
{
    public function it_can_convert_a_key_from_jwk_to_pem()
    {
        $data = $this->dataProvider();

        $this
            ->toPEM($data['jwk'])
            ->shouldReturn($data['pem']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(KeyConverter::class);
    }

    public function let()
    {
        $this->beConstructedWith(new JWKConverter());
    }

    private function dataProvider()
    {
        $jwk = json_decode(
            '{"keys":[{"kty":"RSA","n":"w812qiypKhkfHt1BXtxSSVSCWb2iaz0cZ2JBQlqYy819sQtLtNL5O7S0vH9KzyDiHbY0VjwPA8LneEtR9-KWcfgWjgpOhzLiOzZVQ9WKRDbD5jWOF6Ei9OLoRnS9iN4_AFdqW4P1fZ6O_I3LANsLk-GvZuiDE-aEfDrNcv6UkpfxwIDLVlFlw8hPusbr2i8V_ufeczxcmSK_kB2nHJ6fY385cC8uYBLmxT7GGS6ytm8tUj-Xdd9x0NGtzWlvxVi5A3GLTT4Ryv7pRO7bPrWU-uISSXDCf6OzhV8H328AW9fZouiG1NGR9UleJU4vDlnBTI6hMMTUw3k-fhPujr2VUQ","e":"AQAB"}]}',
            true
        );

        $pem = <<<'EOD'
            -----BEGIN PUBLIC KEY-----
            MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAw812qiypKhkfHt1BXtxS
            SVSCWb2iaz0cZ2JBQlqYy819sQtLtNL5O7S0vH9KzyDiHbY0VjwPA8LneEtR9+KW
            cfgWjgpOhzLiOzZVQ9WKRDbD5jWOF6Ei9OLoRnS9iN4/AFdqW4P1fZ6O/I3LANsL
            k+GvZuiDE+aEfDrNcv6UkpfxwIDLVlFlw8hPusbr2i8V/ufeczxcmSK/kB2nHJ6f
            Y385cC8uYBLmxT7GGS6ytm8tUj+Xdd9x0NGtzWlvxVi5A3GLTT4Ryv7pRO7bPrWU
            +uISSXDCf6OzhV8H328AW9fZouiG1NGR9UleJU4vDlnBTI6hMMTUw3k+fhPujr2V
            UQIDAQAB
            -----END PUBLIC KEY-----
            EOD;

        return [
            'jwk' => $jwk,
            'pem' => str_replace("\n", "\r\n", $pem),
        ];
    }
}
