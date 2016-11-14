<?php

namespace Tests\GBProd\ElasticaProviderBundle\Provider;

use Elastica\Client;
use GBProd\ElasticaProviderBundle\Provider\Provider;
use GBProd\ElasticaProviderBundle\Provider\Handler;
use GBProd\ElasticaProviderBundle\Provider\Registry;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for handler
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $registry;
    private $dispatcher;

    public function setUp()
    {
        $this->client = $this->prophesize(Client::class);
        $this->registry = new Registry();
        $this->dispatcher = $this->prophesize(EventDispatcherInterface::class);
    }

    public function testHandlerRunEveryProviders()
    {
        $this->registry
            ->add(
                new RegistryEntry(
                    $this->createProviderExpectingRun('my_index', 'my_type'),
                    'my_index',
                    'my_type'
                )
            )
            ->add(
                new RegistryEntry(
                    $this->createProviderExpectingRun('my_index', 'my_type_2'),
                    'my_index',
                    'my_type_2'
                )
            )
        ;

        $handler = new Handler($this->registry, $this->dispatcher->reveal());

        $handler->handle($this->client->reveal(), 'my_index', null);
    }

    /**
     * @return provider
     */
    private function createProviderExpectingRun($index, $type)
    {
        $provider = $this->prophesize(Provider::class);

        $provider
            ->run($this->client->reveal(), $index, $type, $this->dispatcher->reveal())
            ->shouldBeCalled()
        ;

        return $provider->reveal();
    }

    public function testHandler()
    {
        $this->registry
            ->add(
                new RegistryEntry(
                    $this->createProviderExpectingRun('my_index', 'my_type'),
                    'my_alias',
                    'my_type'
                )
            )
            ->add(
                new RegistryEntry(
                    $this->createProviderNotExpectingRun('my_index', 'my_type_2'),
                    'my_alias_2',
                    'my_type_2'
                )
            )
        ;

        $handler = new Handler($this->registry, $this->dispatcher->reveal());

        $handler->handle($this->client->reveal(), 'my_index', null, 'my_alias');
    }

    private function createProviderNotExpectingRun($index, $type)
    {
        $provider = $this->prophesize(Provider::class);

        $provider
            ->run($this->client->reveal(), $index, $type, $this->dispatcher->reveal())
            ->shouldNotBeCalled()
        ;

        return $provider->reveal();
    }
}
