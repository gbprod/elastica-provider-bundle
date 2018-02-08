<?php

namespace Tests\GBProd\ElasticaProviderBundle\DependencyInjection;

use GBProd\ElasticaProviderBundle\Command\ProvideCommand;
use GBProd\ElasticaProviderBundle\DependencyInjection\ElasticaProviderExtension;
use GBProd\ElasticaProviderBundle\Provider\Handler;
use GBProd\ElasticaProviderBundle\Provider\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for ElasticaProviderExtension
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ElasticaProviderExtensionTest extends TestCase
{
    private $extension;

    private $container;

    protected function setUp()
    {
        $this->extension = new ElasticaProviderExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);

        $this->container->set(
            'event_dispatcher',
            $this->createMock(EventDispatcherInterface::class)
        );

    }

    /**
     * @dataProvider getServices
     */
    public function testServices($serviceId, $classname)
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertTrue(
            $this->container->has($serviceId)
        );

        $service = $this->container->get($serviceId);

        $this->assertInstanceOf($classname, $service);
    }

    public function getServices()
    {
        return [
            [
                'gbprod.elastica_provider.registry',
                Registry::class,
            ],
            [
                'gbprod.elastica_provider.handler',
                Handler::class,
            ],
            [
                'gbprod.elastica_provider.provide_command',
                ProvideCommand::class,
            ]
        ];
    }

    public function testSetAliasForDefaultClient()
    {
        $config = [
            [
                'default_client' => 'my_client_service',
            ]
        ];

        $this->extension->load($config, $this->container);

        $this->assertTrue(
            $this->container->hasAlias('gbprod.elastica_provider.default_client')
        );

        $this->assertEquals(
            'my_client_service',
            $this->container->getAlias('gbprod.elastica_provider.default_client')
        );
    }
}
