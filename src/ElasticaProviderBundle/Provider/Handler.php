<?php

namespace GBProd\ElasticaProviderBundle\Provider;

use Elastica\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use GBProd\ElasticaProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticaProviderBundle\Event\HasStartedProviding;
use GBProd\ElasticaProviderBundle\Event\HasFinishedProviding;

/**
 * Handle data providing
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class Handler
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param Registry                      $registry
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(Registry $registry, EventDispatcherInterface $dispatcher = null)
    {
        $this->registry   = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle provide command
     */
    public function handle(Client $client, $index, $type, $alias = null)
    {
        $alias = $alias ?: $index;

        $entries = $this->registry->get($alias, $type);

        $this->dispatchHandlingStartedEvent($entries);

        foreach ($entries as $entry) {
            $this->dispatchProvidingStartedEvent($entry);

            $entry->getProvider()->run(
                $client,
                $index,
                $entry->getType(),
                $this->dispatcher
            );

            $this->dispatchProvidingFinishedEvent($entry);
        }
    }

    private function dispatchHandlingStartedEvent(array $entries)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                'elasticsearch.has_started_handling',
                new HasStartedHandling($entries)
            );
        }
    }

    private function dispatchProvidingStartedEvent(RegistryEntry $entry)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                'elasticsearch.has_started_providing',
                new HasStartedProviding($entry)
            );
        }
    }

    private function dispatchProvidingFinishedEvent(RegistryEntry $entry)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                'elasticsearch.has_finished_providing',
                new HasFinishedProviding($entry)
            );
        }
    }
}
