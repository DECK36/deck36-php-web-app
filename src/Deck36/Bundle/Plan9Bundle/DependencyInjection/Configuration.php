<?php

namespace Deck36\Bundle\Plan9Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('deck36_plan9');

        $rootNode
            ->children()
            ->scalarNode('list_pixel_free')->defaultValue('plan9_pixel_free')->end()
            ->arrayNode('playground')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('mobilesize')
            ->info('Size, squared, of the playground square for mobiles.')
            ->defaultValue(2)
            ->end()
            ->scalarNode('desktopsize')
            ->info('Size, squared, of the playground square for others.')
            ->defaultValue(3)
            ->end()
            ->end()
            ->end()
            ->arrayNode('overview')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('solutionimage')->defaultValue(
                'http://i297.photobucket.com/albums/mm206/WayneinWAState/alf_lucky.jpg'
            )->end()
            ->arrayNode('size')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('rows')
            ->defaultValue(200)
            ->end()
            ->scalarNode('cols')
            ->defaultValue(300)
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
