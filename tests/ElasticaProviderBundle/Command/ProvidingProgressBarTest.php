<?php

namespace ElasticaProviderBundle\Command;

use GBProd\ElasticaProviderBundle\Command\ProvidingProgressBar;
use GBProd\ElasticaProviderBundle\Event\HasFinishedProviding;
use GBProd\ElasticaProviderBundle\Event\HasProvidedDocument;
use GBProd\ElasticaProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticaProviderBundle\Event\HasStartedProviding;
use GBProd\ElasticaProviderBundle\Provider\Provider;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for ProvidingProgressBar
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvidingProgressBarTest extends TestCase
{
    /**
     * @var ProvidingProgressBar
     */
    private $testedInstance;

    /**
     * @var EventDispatcher|ObjectProphecy
     */
    private $dispatcher;

    /**
     * @var ConsoleOutput|ObjectProphecy
     */
    private $consoleOutput;

    /**
     * @var Provider|ObjectProphecy
     */
    private $provider;

    /**
     * @var RegistryEntry
     */
    private $entry;

    public function setUp()
    {
        $this->dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->consoleOutput = $this->prophesize(OutputInterface::class);

        $this->testedInstance = new ProvidingProgressBar(
            $this->dispatcher->reveal(),
            $this->consoleOutput->reveal()
        );

        $this->provider = $this->prophesize(Provider::class);
        $this->entry = new RegistryEntry($this->provider->reveal(), 'my_index', 'my_type');
    }

    public function testOnStartedHandlingDisplayNumberOfEntries()
    {
        $event = new HasStartedHandling([
            $this->prophesize(Provider::class)->reveal(),
            $this->prophesize(Provider::class)->reveal(),
            $this->prophesize(Provider::class)->reveal(),
        ]);

        $this
            ->consoleOutput
            ->writeln(Argument::any())
            ->shouldBeCalledTimes(1)
        ;

        $this->testedInstance->onStartedHandling($event);
    }

    public function testOnStartedProvidingDisplayProviderName()
    {
        $event = new HasStartedProviding($this->entry);

        $this
            ->consoleOutput
            ->writeln(Argument::any())
            ->shouldBeCalledTimes(1)
        ;

        $this->testedInstance->onStartedProviding($event);

        $this->assertEmpty($this->testedInstance->progressBar);
    }

    public function testOnStartedProvidingCreateProgressBar()
    {
        $event = new HasStartedProviding($this->entry);

        $this->provider
            ->count(Argument::any())
            ->shouldBeCalled()
            ->willReturn(42)
        ;

        $this->testedInstance->onStartedProviding($event);

        $this->assertNotEmpty($this->testedInstance->progressBar);
        $this->assertInstanceOf(
            ProgressBar::class,
            $this->testedInstance->progressBar
        );

        $this->assertEquals(42, $this->testedInstance->progressBar->getMaxSteps());
    }

    public function testOnProvidedDocumentAdvanceProgress()
    {
        $event = new HasStartedProviding($this->entry);
        $this->provider
            ->count(Argument::any())
            ->shouldBeCalled()
            ->willReturn(42)
        ;
        $this->testedInstance->onStartedProviding($event);
        $this->assertEquals(0, $this->testedInstance->progressBar->getProgress());

        $event = new HasProvidedDocument('id');
        $this->testedInstance->onProvidedDocument($event);
        $this->assertEquals(1, $this->testedInstance->progressBar->getProgress());
    }

    public function testOnFinishedProvidingFinishProgress()
    {
        $this->testedInstance = new ProvidingProgressBar(
            $this->dispatcher->reveal(),
            new NullOutput()
        );

        $event = new HasStartedProviding($this->entry);
        $this->provider
            ->count(Argument::any())
            ->shouldBeCalled()
            ->willReturn(42)
        ;
        $this->testedInstance->onStartedProviding($event);

        $event = new HasFinishedProviding($this->entry);
        $this->testedInstance->onFinishedProviding($event);
        $this->assertEmpty($this->testedInstance->progressBar);
    }
}
