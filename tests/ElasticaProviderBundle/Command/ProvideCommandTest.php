<?php

namespace Tests\GBProd\ElasticaProviderBundle\Command;

use Elastica\Client;
use GBProd\ElasticaProviderBundle\Command\ProvideCommand;
use GBProd\ElasticaProviderBundle\Provider\Handler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for ProvideCommand
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvideCommandTest extends \PHPUnit_Framework_TestCase
{
    private $commandTester;
    private $handler;
    private $client;

    public function setUp()
    {
        $application = new Application();
        $application->add(new ProvideCommand());

        $command = $application->find('elasticsearch:provide');
        $this->commandTester = new CommandTester($command);

        $this->handler = $this->prophesize(Handler::class);

        $container = new Container();
        $container->set('gbprod.elastica_provider.handler', $this->handler->reveal());
        $container->set('event_dispatcher', $this->getMock(EventDispatcherInterface::class));

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
        $this->setExpectedException(\InvalidArgumentException::class);

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
