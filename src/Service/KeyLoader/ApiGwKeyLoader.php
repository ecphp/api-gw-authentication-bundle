<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ecphp
 */

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Exception\ApiGwAuthenticationException;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Throwable;

use function array_key_exists;

/**
 * Class ApiGwKeyLoader.
 *
 * phpcs:disable Generic.Files.LineLength.TooLong
 */
final class ApiGwKeyLoader implements KeyLoaderInterface
{
    private const API_GW_ACCEPTANCE = 'https://api.acceptance.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json';

    private const API_GW_INTRA = 'https://intrapi.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json';

    private const API_GW_PRODUCTION = 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json';

    private const LOCAL_FAILSAFE_PATH = '/../../Resources/keys';

    private array $environment;

    private ClientInterface $httpClient;

    private KeyConverterInterface $keyConverter;

    private static array $mapping = [
        [
            'env' => 'production',
            KeyLoaderInterface::TYPE_PUBLIC => self::API_GW_PRODUCTION,
            KeyLoaderInterface::TYPE_PRIVATE => '',
            'failsafe' => [
                KeyLoaderInterface::TYPE_PUBLIC => self::LOCAL_FAILSAFE_PATH . '/production/jwks.json',
                KeyLoaderInterface::TYPE_PRIVATE => '',
            ],
        ],
        [
            'env' => 'intra',
            KeyLoaderInterface::TYPE_PUBLIC => self::API_GW_INTRA,
            KeyLoaderInterface::TYPE_PRIVATE => '',
            'failsafe' => [
                KeyLoaderInterface::TYPE_PUBLIC => self::LOCAL_FAILSAFE_PATH . '/intra/jwks.json',
                KeyLoaderInterface::TYPE_PRIVATE => '',
            ],
        ],
        [
            'env' => 'acceptance',
            KeyLoaderInterface::TYPE_PUBLIC => self::API_GW_ACCEPTANCE,
            KeyLoaderInterface::TYPE_PRIVATE => '',
            'failsafe' => [
                KeyLoaderInterface::TYPE_PUBLIC => self::LOCAL_FAILSAFE_PATH . '/acceptance/jwks.json',
                KeyLoaderInterface::TYPE_PRIVATE => '',
            ],
        ],
    ];

    private string $projectDir;

    private RequestFactoryInterface $requestFactory;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        KeyConverterInterface $keyConverter,
        string $projectDir,
        array $configuration
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->keyConverter = $keyConverter;
        $this->projectDir = $projectDir;
        $this->environment = $this->getEnvironment($configuration['defaults']['env'], $configuration['envs']);
    }

    public function getPassphrase(): string
    {
        // Todo: Not supported yet.
        return '';
    }

    public function getPublicKey(): string
    {
        return $this->environment[KeyLoaderInterface::TYPE_PUBLIC] ?? '';
    }

    public function getSigningKey(): string
    {
        return $this->environment[KeyLoaderInterface::TYPE_PRIVATE] ?? '';
    }

    public function loadKey($type): string
    {
        $publicKey = $this->getPublicKey();
        $signingKey = $this->getSigningKey();
        $passPhrase = $this->getPassphrase();

        $key = KeyLoaderInterface::TYPE_PUBLIC === $type ? $publicKey : $signingKey;

        if ('user' === $this->environment['env']) {
            $keyPathCandidateParts = $this->findFirstFileExist($key);

            if ([] !== $keyPathCandidateParts) {
                $prefix = current($keyPathCandidateParts);

                return (new RawKeyLoader($prefix . $signingKey, $prefix . $publicKey, $passPhrase))
                    ->loadKey($type);
            }
        }

        try {
            $key = (new JWKSKeyLoader($this, $this->httpClient, $this->requestFactory, $this->keyConverter))
                ->loadKey($type);
        } catch (Throwable $e) {
            $key = $this->loadFailsafeKey($type);
        }

        return $key;
    }

    private function findFirstFileExist(string $key): array
    {
        $candidates = array_map(
            static fn (string $directory): array => [$directory, $key],
            [
                $this->projectDir,
                __DIR__,
            ]
        );

        foreach ($candidates as $candidate) {
            if (true === file_exists(implode('', $candidate))) {
                return $candidate;
            }
        }

        return [];
    }

    private function getEnvironment(string $env, array $configuredEnvs): array
    {
        $envs = [];

        foreach ($configuredEnvs as $name => $data) {
            $envs[] = [
                'env' => $name,
                KeyLoaderInterface::TYPE_PUBLIC => $data[KeyLoaderInterface::TYPE_PUBLIC],
                KeyLoaderInterface::TYPE_PRIVATE => $data[KeyLoaderInterface::TYPE_PRIVATE],
                'failsafe' => [
                    KeyLoaderInterface::TYPE_PUBLIC => $data['failsafe'][KeyLoaderInterface::TYPE_PUBLIC] ?? $data[KeyLoaderInterface::TYPE_PUBLIC],
                    KeyLoaderInterface::TYPE_PRIVATE => $data['failsafe'][KeyLoaderInterface::TYPE_PRIVATE] ?? $data[KeyLoaderInterface::TYPE_PRIVATE],
                ],
            ];
        }

        foreach (array_merge(self::$mapping, $envs) as $mapping) {
            if ($mapping['env'] === $env) {
                return $mapping;
            }
        }
    }

    private function getFailsafePrivateKey(): string
    {
        return $this->environment['failsafe'][KeyLoaderInterface::TYPE_PRIVATE];
    }

    private function getFailsafePublicKey(): string
    {
        return $this->environment['failsafe'][KeyLoaderInterface::TYPE_PUBLIC];
    }

    private function loadFailsafeKey(string $type): string
    {
        $key = KeyLoaderInterface::TYPE_PUBLIC === $type ?
            $this->getFailsafePublicKey() :
            $this->getFailsafePrivateKey();

        $keyPathCandidateParts = $this->findFirstFileExist($key);

        // Todo: Remove duplicated code in here and JWKSKeyLoader.
        $jwksArray = json_decode(file_get_contents(implode('', $keyPathCandidateParts)), true);

        if (false === array_key_exists('keys', $jwksArray)) {
            throw new ApiGwAuthenticationException(
                sprintf('Invalid JWKS format of %s key at %s.', $type, $key)
            );
        }

        if ([] === $jwksArray['keys']) {
            throw new ApiGwAuthenticationException(
                sprintf('Invalid JWKS format of %s key at %s, keys array is empty.', $type, $key)
            );
        }

        return current($this->keyConverter->fromJWKStoPEMS($jwksArray['keys']));
    }
}
