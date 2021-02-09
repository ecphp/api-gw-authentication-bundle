<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticatorBundle\Security\Core\User;

final class ApiGwAuthenticatorUser implements ApiGwAuthenticatorUserInterface
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

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        return $this->getStorage()[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $key, $default = null)
    {
        return $this->getStorage()[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(): array
    {
        return $this->getStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return ['IS_AUTHENTICATED_FULLY'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
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
