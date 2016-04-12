<?php

namespace Tests\GBProd\ElasticaProviderBundle;

use GBProd\ElasticaProviderBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use GBProd\ElasticaProviderBundle\ElasticaProviderBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for Bundle
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticaProviderBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            ElasticaProviderBundle::class,
            new ElasticaProviderBundle()
        );
    }

    public function testBuildAddCompilerPass()
    {
        $container = $this->getMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(providerCompilerPass::class))
        ;

        $bundle = new ElasticaProviderBundle();
        $bundle->build($container);
    }
}
