<?php

namespace LePhare\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
        $rootNode = $treeBuilder->root('lephare_menu');

        $rootNode
            ->children()
                ->arrayNode('menus')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('options')
                                ->children()
                                    ->booleanNode('remove_empty')->defaultValue(true)->end()
                                    ->scalarNode('reserved_role')->end()
                                ->end()
                            ->end()
                            ->variableNode('definition')
                                ->isRequired()
                                ->defaultValue([])
                                ->validate()
                                    ->ifTrue(function($element) { return !is_array($element); })
                                    ->thenInvalid('The menu element must be an array.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
