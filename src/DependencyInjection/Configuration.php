<?php

declare(strict_types=1);

namespace EcPhp\ApiGwAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('api_gw_authentication');

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        /** @phpstan-ignore-next-line */
        $rootNode
            ->children()
            ->arrayNode('defaults')
            ->children()
            ->enumNode('env')
            ->values(['production', 'acceptance', 'intra', 'user'])
            ->defaultValue('production')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->end()
            ->arrayNode('envs')
            ->defaultValue($this->getDefaultEnvs())
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
            ->scalarNode('public')->end()
            ->scalarNode('private')->defaultValue('')->end()
            ->arrayNode('failsafe')
            ->children()
            ->scalarNode('public')->defaultValue('')->end()
            ->scalarNode('private')->defaultValue('')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }

    private function getDefaultEnvs(): array
    {
        return [
            'acceptance' => [
                'public' => 'https://api.acceptance.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
                'private' => null,
                'failsafe' => [
                    'public' => '',
                    'private' => '',
                ],
            ],
            'production' => [
                'public' => 'https://api.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
                'private' => null,
                'failsafe' => [
                    'public' => '',
                    'private' => '',
                ],
            ],
            'intra' => [
                'public' => 'https://intrapi.tech.ec.europa.eu/federation/oauth/token/.well-known/jwks.json',
                'private' => null,
                'failsafe' => [
                    'public' => '',
                    'private' => '',
                ],
            ],
        ];
    }
}
