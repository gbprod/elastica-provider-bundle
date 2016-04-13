<?php

namespace Tests\GBProd\ElasticaProviderBundle\Provider;

use Elastica\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use GBProd\ElasticaProviderBundle\Provider\BulkProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for abstract data provider
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class BulkProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRunExecutePopulate()
    {
        $provider = $this->getMockForAbstractClass(BulkProvider::class);

        $provider
            ->expects($this->once())
            ->method('populate')
        ;

        $provider->run(
            $this->getClient('index'),
            'index',
            'type',
            $this->getMock(EventDispatcherInterface::class)
        );
    }

    /**
     * @return Client
     */   
    private function getClient($index)
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $client;
    }

    public function testIndexWithIndexAndType()
    {
        $client = $this->newClientExpectingBulk(
            [
                [
                    'index' => [
                        '_index' => 'my_index',
                        '_type'  => 'my_type',
                        '_id'    => 'my_id',
                    ]
                ],
                [
                    'foo' => 'bar',
                ]
            ]
        );

        $provider = $this->getMockForAbstractClass(BulkProvider::class);
        $provider
            ->expects($this->once())
            ->method('populate')
            ->will(
            $this->returnCallback(
                function () use ($provider) {
                    $provider->index(
                        'my_id',
                        ['foo' => 'bar']
                    );
                }
            )
            )
        ;

        $provider->run(
            $client,
            'my_index',
            'my_type',
            $this->getMock(EventDispatcherInterface::class)
        );
    }

    private function newClientExpectingBulk($content)
    {
        $client = $this->getClient('my_index');

        $client
            ->expects($this->once())
            ->method('bulk')
            ->with($content)
        ;

        return $client;
    }

    public function testIndexRunBulkTwiceIfMoreThanBatchSize()
    {
        $provider = $this->getMockForAbstractClass(BulkProvider::class);

        $client = $this->getClient('my_index');
        $client
            ->expects($this->exactly(3))
            ->method('bulk')
        ;

        $provider->changeBulkSize(50);
        $provider
            ->expects($this->once())
            ->method('populate')
            ->will(
            $this->returnCallback(
                function () use ($provider) {
                    for($i = 0; $i < 150; $i++) {
                        $provider->index(
                        'my_id',
                        ['foo' => 'bar']
                        );
                    }
                }
            )
        );

        $provider->run(
            $client,
            'my_index',
            'my_type',
            $this->getMock(EventDispatcherInterface::class)
        );
    }

    public function testCountIsNull()
    {
        $provider = $this->getMockForAbstractClass(BulkProvider::class);

        $this->assertNull($provider->count());
    }

    public function testDelete()
    {
        $bulk = [
            [
                'delete' => [
                    '_index' => 'my_index',
                    '_type'  => 'my_type',
                    '_id'    => 'my_id',
                ]
            ]
        ];

        $client = $this->newClientExpectingBulk($bulk);

        $provider = $this->newProviderForBulk('delete', 'my_id', null);

        $provider->run(
            $client,
            'my_index',
            'my_type',
            $this->getMock(EventDispatcherInterface::class)
        );
    }

    private function newProviderForBulk($method, $id, $content)
    {
        $provider = $this->getMockForAbstractClass(BulkProvider::class);
        $provider
            ->expects($this->once())
            ->method('populate')
            ->will(
            $this->returnCallback(
                function () use ($provider, $method, $id, $content) {
                    $provider->{$method}($id, $content);
                }
            )
            )
        ;

        return $provider;
    }

    public function testCreate()
    {
        $client = $this->newClientExpectingBulk(
            [
                [
                    'create' => [
                        '_index' => 'my_index',
                        '_type'  => 'my_type',
                        '_id'    => 'my_id',
                    ]
                ],
                [
                    'foo' => 'bar',
                ]
            ]
        );

        $provider = $this->newProviderForBulk('create', 'my_id', ['foo' => 'bar']);

        $provider->run(
            $client,
            'my_index',
            'my_type',
            $this->getMock(EventDispatcherInterface::class)
        );
    }

    public function testUpdate()
    {
        $client = $this->newClientExpectingBulk(
            [
                [
                    'update' => [
                        '_index' => 'my_index',
                        '_type'  => 'my_type',
                        '_id'    => 'my_id',
                   ]
                ],
                [
                    'doc' => ['foo' => 'bar'],
                ]
            ]
        );

        $provider = $this->newProviderForBulk('update', 'my_id', ['foo' => 'bar']);

        $provider->run(
            $client,
            'my_index',
            'my_type',
            $this->getMock(EventDispatcherInterface::class)
        );
    }
}
