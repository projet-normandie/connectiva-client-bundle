<?php

namespace ProjetNormandie\ConnectivaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('projetnormandie_connectiva_api');

        $rootNode = $treeBuilder->root('projetnormandie_connectiva_api');
        $rootNode->children()
                ->arrayNode("clients")
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('url')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->scalarNode('username')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->scalarNode('password')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}