<?php

namespace GBProd\ElasticaProviderBundle\Command;

use GBProd\ElasticaProviderBundle\Event\HasFinishedProviding;
use GBProd\ElasticaProviderBundle\Event\HasProvidedDocument;
use GBProd\ElasticaProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticaProviderBundle\Event\HasStartedProviding;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Progress bar for providing
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvidingProgressBar
{
    const PROGRESS_BAR_TEMPLATE = ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ProgressBar
     *
     * public for test purpose
     */
    public $progressBar;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param OutputInterface          $output
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        OutputInterface $output
    ) {
        $this->output     = $output;
        $this->dispatcher = $dispatcher;

        $this->listen('elasticsearch.has_started_handling', 'onStartedHandling');
        $this->listen('elasticsearch.has_started_providing', 'onStartedProviding');
        $this->listen('elasticsearch.has_provided_document', 'onProvidedDocument');
        $this->listen('elasticsearch.has_finished_providing', 'onFinishedProviding');
    }

    private function listen($eventName, $function)
    {
        $this->dispatcher->addListener($eventName, [$this, $function]);
    }

    public function onStartedHandling(HasStartedHandling $event)
    {
        $this->output->writeln(
            sprintf(
                '<info>Start running <comment>%d</comment> providers</info>',
                count($event->getEntries())
            )
        );
    }

    public function onStartedProviding(HasStartedProviding $event)
    {
        $this->output->writeln(sprintf(
            '<info> - Running <comment>%s</comment> provider into <comment>%s/%s</comment></info>',
            get_class($event->getEntry()->getProvider()),
            $event->getEntry()->getIndex(),
            $event->getEntry()->getType()
        ));

        $count = $event->getEntry()->getProvider()->count();
        if (null !== $count && $count > 0) {
            $this->progressBar = new ProgressBar($this->output, $count);
            $this->progressBar->setFormat(self::PROGRESS_BAR_TEMPLATE);
        }
    }

    public function onProvidedDocument(HasProvidedDocument $event)
    {
        if ($this->progressBar) {
            $this->progressBar->advance();
        }
    }

    public function onFinishedProviding(HasFinishedProviding $event)
    {
        if ($this->progressBar) {
            $this->progressBar->finish();
        }

        $this->output->writeln('');
        $this->progressBar = null;
    }
}
