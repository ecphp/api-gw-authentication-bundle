<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ecphp
 */

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter;

use CoderCat\JWKToPEM\JWKConverter;

final class KeyConverter implements KeyConverterInterface
{
    private JWKConverter $jwkConverter;

    public function __construct(JWKConverter $jwkConverter)
    {
        $this->jwkConverter = $jwkConverter;
    }

    public function fromJWKStoPEMS(array $jwks): array
    {
        return $this->jwkConverter->multipleToPEM($jwks);
    }

    public function fromJWKtoPEM(array $jwk): string
    {
        return $this->jwkConverter->toPEM($jwk);
    }
}
