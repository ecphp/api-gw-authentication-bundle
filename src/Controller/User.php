<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Controller;

use EcPhp\ApiGwAuthenticationBundle\Security\Core\User\ApiGwAuthenticationUserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

final class User
{
    public function __invoke(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if ($user instanceof ApiGwAuthenticationUserInterface) {
            return new JsonResponse($user->getAttributes());
        }

        return new JsonResponse([], 404);
    }
}
