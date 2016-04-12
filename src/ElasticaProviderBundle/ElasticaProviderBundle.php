<?php

namespace GBProd\ElasticaProviderBundle;

use GBProd\ElasticaProviderBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticaProviderBundle extends Bundle
{
    /**
     * {inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new providerCompilerPass());
    }
}