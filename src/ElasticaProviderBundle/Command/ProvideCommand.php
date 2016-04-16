<?php

namespace GBProd\ElasticaProviderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Command to run providing
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ProvideCommand extends ContainerAwareCommand
{
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
            ->addOption('client', null, InputOption::VALUE_REQUIRED, 'Client to use (if not default)', null)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $this->getContainer()
            ->get('gbprod.elastica_provider.handler')
        ;

        $client = $this->getClient($input->getOption('client'));

        $index = $input->getArgument('index');
        $type  = $input->getArgument('type');

        $output->writeln(sprintf(
            '<info>Providing <comment>%s/%s</comment> for client <comment>%s</comment></info>',
            $index ?: '*',
            $type ?: '*',
            $input->getOption('client')
        ));

        $this->initializeProgress($output);

        $handler->handle($client, $index, $type);
    }

    private function getClient($clientName)
    {
        $clientName = $clientName ?: 'gbprod.elastica_provider.default_client';
        
        $client = $this->getContainer()
            ->get(
                $clientName,
                ContainerInterface::NULL_ON_INVALID_REFERENCE
            )
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
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        new ProvidingProgressBar($dispatcher, $output);
    }
}
