<?php

namespace Tests\GBProd\ElasticaProviderBundle\DependencyInjection\Compiler;

use GBProd\ElasticaProviderBundle\Provider\Provider;
use GBProd\ElasticaProviderBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler path to register providers
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProviderCompilerPassTest extends TestCase
{
    private $testedInstance;

    private $container;

    private $registryDefinition;

    public function setUp()
    {
        $this->testedInstance     = new providerCompilerPass();
        $this->container          = new ContainerBuilder();
        $this->registryDefinition = new Definition();
    }

    public function testShouldRegisterTaggedproviders()
    {
        $this->container->setDefinition(
            'gbprod.elastica_provider.registry',
            $this->registryDefinition
        );

        $this->container->setDefinition(
            'provider.foo.bar',
            $this->newproviderDefinition('foo', 'bar')
        );

        $this->container->setDefinition(
            'provider.bar.foo',
            $this->newproviderDefinition('fizz', 'buzz')
        );

        $this->testedInstance->process($this->container);

        $calls = $this->registryDefinition->getMethodCalls();

        $this->assertEquals('add', $calls[0][0]);
        $this->assertInstanceOf(Definition::class, $calls[0][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][0]->getArgument(0));
        $this->assertEquals('provider.foo.bar', $calls[0][1][0]->getArgument(0)->__toString());
        $this->assertEquals('foo', $calls[0][1][0]->getArgument(1));
        $this->assertEquals('bar', $calls[0][1][0]->getArgument(2));

        $this->assertEquals('add', $calls[1][0]);
        $this->assertInstanceOf(Definition::class, $calls[1][1][0]);
        $this->assertEquals('provider.bar.foo', $calls[1][1][0]->getArgument(0)->__toString());
        $this->assertEquals('fizz', $calls[1][1][0]->getArgument(1));
        $this->assertEquals('buzz', $calls[1][1][0]->getArgument(2));
    }

    private function newproviderDefinition($index, $type)
    {
        $tag = ['index' => $index, 'type' => $type];

        $definition = new Definition(Provider::class);
        $definition->addTag('elastica.provider', $tag);

        return $definition;
    }

    public function testThrowsExceptionIfNotprovider()
    {
        $this->container->setDefinition(
            'gbprod.elastica_provider.registry',
            $this->registryDefinition
        );

        $definition = new Definition(\stdClass::class);
        $definition->addTag(
            'elastica.provider',
            ['index' => 'foo', 'type' => 'bar']
        );

        $this->container->setDefinition(
            'provider.foo.bar',
            $definition
        );

        $this->expectException(\InvalidArgumentException::class);

        $this->testedInstance->process($this->container);
    }

    public function testThrowsExceptionIfBadTag()
    {
        $this->container->setDefinition(
            'gbprod.elastica_provider.registry',
            $this->registryDefinition
        );

        $definition = new Definition(Provider::class);
        $definition->addTag(
            'elastica.provider',
            ['type' => 'my-type']
        );

        $this->container->setDefinition(
            'provider.foo.bar',
            $definition
        );

        $this->expectException(\InvalidArgumentException::class);

        $this->testedInstance->process($this->container);
    }

    public function testNeverCallGetDefinitionIfServiceNotSet()
    {
        $container = $this->createMock(ContainerBuilder::class);

        $container
            ->expects($this->any())
            ->method('hasDefinition')
            ->with('gbprod.elastica_provider.registry')
            ->willReturn(false)
        ;

        $container
            ->expects($this->never())
            ->method('getDefinition')
            ->with('gbprod.elastica_provider.registry')
        ;

        $this->testedInstance->process($container);
    }
}
