<?php

namespace GBProd\ElasticaProviderBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Extension class for ElasticaProviderExtension
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticaProviderExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
    
        if (null !== $config['default_client']) {
            $container->setAlias(
                'gbpord.elastica_provider.default_client', 
                $config['default_client']
            );
        }
        
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');
    }
}