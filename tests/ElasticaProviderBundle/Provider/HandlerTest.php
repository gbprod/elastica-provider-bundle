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
        $this->client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->registry = new Registry();
        $this->dispatcher = $this->getMock(EventDispatcherInterface::class);
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

        $handler = new Handler($this->registry, $this->dispatcher);

        $handler->handle($this->client, 'my_index', null);
    }

    /**
     * @return provider
     */
    private function createProviderExpectingRun($index, $type)
    {
        $provider = $this->getMock(Provider::class);

        $provider
            ->expects($this->once())
            ->method('run')
            ->with($this->client, $index, $type, $this->dispatcher)
        ;

        return $provider;
    }
}
