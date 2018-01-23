<?php

namespace Tests\GBProd\ElasticaProviderBundle\Command;

use Elastica\Client;
use GBProd\ElasticaProviderBundle\Command\ProvideCommand;
use GBProd\ElasticaProviderBundle\Provider\Handler;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for ProvideCommand
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvideCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var Handler|ObjectProphecy
     */
    private $handler;

    /**
     * @var EventDispatcherInterface|ObjectProphecy
     */
    private $dispatcher;

    /**
     * @var Client|ObjectProphecy
     */
    private $client;

    public function setUp()
    {
        $application = new Application();

        $this->handler = $this->prophesize(Handler::class);
        $this->dispatcher = $this->prophesize(EventDispatcherInterface::class);

        $application->add(new ProvideCommand($this->handler->reveal(), $this->dispatcher->reveal()));

        $command = $application->find('elasticsearch:provide');
        $this->commandTester = new CommandTester($command);

        $container = new Container();
        $this->client = $this->prophesize(Client::class);
        $container->set('gbprod.elastica_provider.default_client', $this->client->reveal());

        $command->setContainer($container);
    }

    public function testExecute()
    {
        $this->handler
            ->handle($this->client->reveal(), 'my_index', 'my_type', null)
            ->shouldBeCalled()
        ;

        $this->commandTester->execute([
            'command' => 'elasticsearch:provide',
            'index'   => 'my_index',
            'type'    => 'my_type',
        ]);
    }

    public function testExecuteThrowExceptionIfClientNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute([
            'command'  => 'elasticsearch:provide',
            'index'    => 'my_index',
            'type'     => 'my_type',
            '--client' => 'foo',
        ]);
    }

    public function testExecuteWithAlias()
    {
        $this->handler
            ->handle($this->client->reveal(), 'my_index', 'my_type', 'my_alias')
            ->shouldBeCalled()
        ;

        $this->commandTester->execute([
            'command' => 'elasticsearch:provide',
            'index'   => 'my_index',
            'type'    => 'my_type',
            '--alias' => 'my_alias',
        ]);
    }

    public function testExecuteWithAliasButNoIndex()
    {
        $this->handler
            ->handle($this->client->reveal(), null, null, null)
            ->shouldBeCalled()
        ;

        $this->commandTester->execute([
            'command' => 'elasticsearch:provide',
            '--alias' => 'my_alias',
        ]);
    }
}
