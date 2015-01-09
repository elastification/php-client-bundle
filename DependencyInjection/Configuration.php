<?php

namespace Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('elastification_php_client');

        $this->addSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     * @author Daniel Wendlandt
     */
    private function addSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('host')
                    ->isRequired()
                ->end()

                ->scalarNode('port')
                    ->defaultValue(9200)
                ->end()

                ->scalarNode('protocol')
                    ->defaultValue('http')
                ->end()

                ->scalarNode('elasticsearch_version')
                    ->defaultNull()
                ->end()

                ->scalarNode('repository_serializer_dic_id')
                    ->defaultNull()
                ->end()

                ->booleanNode('replace_version_of_tagged_requests')
                    ->defaultValue(false)
                ->end()

                ->booleanNode('logging_enabled')
                    ->defaultValue('%kernel.debug%')
                ->end()

                ->booleanNode('profiler_enabled')
                    ->defaultValue(true)
                ->end()

                ->arrayNode('jms_serializer_class_map')
                    ->canBeUnset()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('index')
                                ->isRequired()
                            ->end()
                            ->scalarNode('type')
                                ->isRequired()
                            ->end()
                            ->scalarNode('class')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ->end();
    }

}
