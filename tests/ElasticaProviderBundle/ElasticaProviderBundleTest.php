<?php

namespace Tests\GBProd\ElasticaProviderBundle;

use GBProd\ElasticaProviderBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use GBProd\ElasticaProviderBundle\ElasticaProviderBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for Bundle
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticaProviderBundleTest extends TestCase
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
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ProviderCompilerPass::class))
        ;

        $bundle = new ElasticaProviderBundle();
        $bundle->build($container);
    }
}
