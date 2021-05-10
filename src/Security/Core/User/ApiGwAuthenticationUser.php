<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Security\Core\User;

final class ApiGwAuthenticationUser implements ApiGwAuthenticationUserInterface
{
    public string $sub;

    /**
     * The user storage.
     *
     * @var array<mixed>
     */
    private array $storage;

    /**
     * @param array<mixed> $data
     */
    public function __construct(string $username, array $data = [])
    {
        $this->sub = $username;
        $this->storage = $data;
    }

    public static function createFromPayload($username, array $payload)
    {
        return new self($username, $payload);
    }

    public function eraseCredentials(): void
    {
    }

    public function get(string $key, $default = null)
    {
        return $this->getStorage()[$key] ?? $default;
    }

    public function getAttribute(string $key, $default = null)
    {
        return $this->getStorage()[$key] ?? $default;
    }

    public function getAttributes(): array
    {
        return $this->getStorage();
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getRoles(): array
    {
        return ['IS_AUTHENTICATED_FULLY'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->sub;
    }

    /**
     * Get the storage.
     *
     * @return array<mixed>
     */
    private function getStorage(): array
    {
        return $this->storage;
    }
}
