<?php

namespace GBProd\ElasticaProviderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('elastica_provider');
        
        $rootNode
            ->children()
                ->scalarNode('default_client')
                    ->defaultValue(null)
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}