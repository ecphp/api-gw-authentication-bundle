<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

final class User
{
    public function __invoke(Security $security): JsonResponse
    {
        return new JsonResponse($security->getUser()->getAttributes());
    }
}
