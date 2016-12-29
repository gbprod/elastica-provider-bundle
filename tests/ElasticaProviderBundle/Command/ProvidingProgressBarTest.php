<?php

namespace ElasticaProviderBundle\Command;

use GBProd\ElasticaProviderBundle\Command\ProvidingProgressBar;
use GBProd\ElasticaProviderBundle\Event\HasFinishedProviding;
use GBProd\ElasticaProviderBundle\Event\HasProvidedDocument;
use GBProd\ElasticaProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticaProviderBundle\Event\HasStartedProviding;
use GBProd\ElasticaProviderBundle\Provider\Provider;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for ProvidingProgressBar
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvidingProgressBarTest extends \PHPUnit_Framework_TestCase
{
    private $testedInstance;
    private $dispatcher;
    private $consoleOutput;
    private $provider;
    private $entry;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->consoleOutput = $this->createMock(OutputInterface::class);

        $this->testedInstance = new ProvidingProgressBar(
            $this->dispatcher,
            $this->consoleOutput
        );

        $this->provider = $this->createMock(Provider::class);
        $this->entry = new RegistryEntry($this->provider, 'my_index', 'my_type');
    }

    public function testOnStartedHandlingDisplayNumberOfEntries()
    {
        $event = new HasStartedHandling([
            $this->createMock(Provider::class),
            $this->createMock(Provider::class),
            $this->createMock(Provider::class),
        ]);

        $this
            ->consoleOutput
            ->expects($this->once())
            ->method('writeln')
            ->with(
                $this->stringContains('<comment>3</comment>')
            )
        ;

        $this->testedInstance->onStartedHandling($event);
    }

    public function testOnStartedProvidingDisplayProviderName()
    {
        $event = new HasStartedProviding($this->entry);

        $this
            ->consoleOutput
            ->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains(get_class($this->provider)))
        ;

        $this->testedInstance->onStartedProviding($event);

        $this->assertEmpty($this->testedInstance->progressBar);
    }

    public function testOnStartedProvidingCreateProgressBar()
    {
        $event = new HasStartedProviding($this->entry);

        $this->provider
            ->expects($this->any())
            ->method('count')
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
        $event = new HasProvidedDocument('id');

        $this->setProvressBarExpectsMethod('advance');

        $this->testedInstance->onProvidedDocument($event);
    }

    private function setProvressBarExpectsMethod($method)
    {
        $this->testedInstance->progressBar = $this
            ->createMock(ProgressBar::class, [], [$this->consoleOutput])
        ;

        $this->testedInstance
            ->progressBar
            ->expects($this->once())
            ->method($method)
        ;
    }

    public function testOnFinishedProvidingFinishProgress()
    {
        $event = new HasFinishedProviding($this->entry);

        $this->setProvressBarExpectsMethod('finish');

        $this->testedInstance->onFinishedProviding($event);

        $this->assertEmpty($this->testedInstance->progressBar);
    }
}
