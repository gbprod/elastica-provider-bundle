<?php

namespace GBProd\ElasticaProviderBundle\Provider;

use Elastica\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface for provider
 *
 * @author gbprod <contact@gb-prod.fr>
 */
interface Provider
{
    /**
     * Populate index
     *
     * @param Client                   $client
     * @param string                   $index
     * @param string                   $type
     * @param EventDispatcherInterface $dispatcher
     *
     * @return void
     */
    public function run(Client $client, $index, $type, EventDispatcherInterface $dispatcher);

    /**
     * Number of documents that should be indexed
     *
     * @return int
     */
    public function count();
}
