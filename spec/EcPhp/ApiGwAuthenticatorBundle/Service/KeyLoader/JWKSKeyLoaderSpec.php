<?php

declare(strict_types=1);

namespace spec\EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader;

use EcPhp\ApiGwAuthenticationBundle\Service\KeyConverter\KeyConverterInterface;
use EcPhp\ApiGwAuthenticationBundle\Service\KeyLoader\JWKSKeyLoader;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class JWKSKeyLoaderSpec extends ObjectBehavior
{
    public function it_can_load_a_remote_key()
    {
        $this
            ->loadKey(KeyLoaderInterface::TYPE_PUBLIC)
            ->shouldReturn('foo');
    }

    public function it_can_throw_when_status_code_is_not_200(KeyLoaderInterface $keyLoader, HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, ResponseInterface $response)
    {
        $this->prepareDeps($keyLoader, $httpClient, $keyConverter, $response);

        $response
            ->getStatusCode()
            ->willReturn(500);

        $this
            ->shouldThrow(Exception::class)
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function it_can_throw_when_the_request_failed(KeyLoaderInterface $keyLoader, HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, ResponseInterface $response)
    {
        $this->prepareDeps($keyLoader, $httpClient, $keyConverter, $response);

        $httpClient
            ->request('GET', KeyLoaderInterface::TYPE_PUBLIC)
            ->willThrow(new TransportException('Error'));

        $this
            ->shouldThrow(TransportExceptionInterface::class)
            ->during('loadKey', [KeyLoaderInterface::TYPE_PUBLIC]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JWKSKeyLoader::class);

        $this
            ->getPublicKey()
            ->shouldReturn(KeyLoaderInterface::TYPE_PUBLIC);

        $this
            ->getSigningKey()
            ->shouldReturn(KeyLoaderInterface::TYPE_PRIVATE);

        $this
            ->getPassphrase()
            ->shouldReturn('passphrase');
    }

    public function let(KeyLoaderInterface $keyLoader, HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, ResponseInterface $response)
    {
        $this->prepareDeps($keyLoader, $httpClient, $keyConverter, $response);
        $this->beConstructedWith($keyLoader, $httpClient, $keyConverter);
    }

    private function prepareDeps(KeyLoaderInterface $keyLoader, HttpClientInterface $httpClient, KeyConverterInterface $keyConverter, ResponseInterface $response): void
    {
        $keyLoader
            ->getSigningKey()
            ->willReturn(KeyLoaderInterface::TYPE_PRIVATE);

        $keyLoader
            ->getPublicKey()
            ->willReturn(KeyLoaderInterface::TYPE_PUBLIC);

        $keyLoader
            ->getPassphrase()
            ->willReturn('passphrase');

        $response
            ->getStatusCode()
            ->willReturn(200);

        $response
            ->toArray()
            ->willReturn(['keys' => []]);

        $keyConverter
            ->fromJWKStoPEMS([])
            ->willReturn(['foo']);

        $httpClient
            ->request('GET', KeyLoaderInterface::TYPE_PUBLIC)
            ->willReturn(
                $response
            );
    }
}
