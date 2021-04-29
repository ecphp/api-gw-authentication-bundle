<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface as OriginalKeyLoaderInterface;

/**
 * Interface KeyLoaderInterface.
 *
 * This interface needs to be removed at the next major version of lexik/lexikJwtAuthBundle.
 *
 * @see https://github.com/lexik/LexikJWTAuthenticationBundle/pull/832
 */
interface KeyLoaderInterface extends OriginalKeyLoaderInterface
{
    public function getPublicKey(): string;

    public function getSigningKey(): string;
}
