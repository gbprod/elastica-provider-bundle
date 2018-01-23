<?php

namespace GBProd\ElasticaProviderBundle\Command;

use Elastica\Client;
use GBProd\ElasticaProviderBundle\Provider\Handler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Command to run providing
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvideCommand extends ContainerAwareCommand
{
    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param Handler                  $handler
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Handler $handler,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
        $this->handler = $handler;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elasticsearch:provide')
            ->setDescription('Provide data to Elasticsearch')
            ->addArgument('index', InputArgument::OPTIONAL, 'Index to provide')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type to provide')
            ->addOption('client', null, InputOption::VALUE_REQUIRED, 'Client to use (if not default)')
            ->addOption('alias', null, InputOption::VALUE_REQUIRED, 'Alias to use instead of index name for providers (index is required to use this option)')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getClient($input->getOption('client'));

        $index = $input->getArgument('index');
        $type  = $input->getArgument('type');
        $alias = (null !== $index) ? $input->getOption('alias') : null; // alias usage only if index is set

        $output->writeln(sprintf(
            '<info>Providing <comment>%s/%s</comment> for client <comment>%s</comment></info>',
            $index ?: '*',
            $type ?: '*',
            $input->getOption('client')
        ));

        $this->initializeProgress($output);

        $this->handler->handle($client, $index, $type, $alias);
    }

    /**
     * @param string $clientName
     *
     * @return Client
     */
    private function getClient($clientName)
    {
        $clientName = $clientName ?: 'gbprod.elastica_provider.default_client';

        $client = $this->getContainer()
            ->get($clientName, ContainerInterface::NULL_ON_INVALID_REFERENCE)
        ;

        if (!$client) {
            throw new \InvalidArgumentException(sprintf(
                'No client "%s" found',
                $clientName
            ));
        }

        return $client;
    }

    private function initializeProgress(OutputInterface $output)
    {
        new ProvidingProgressBar($this->eventDispatcher, $output);
    }
}
