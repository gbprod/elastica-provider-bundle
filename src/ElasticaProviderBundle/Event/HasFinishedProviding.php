<?php

namespace GBProd\ElasticaProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;

/**
 * Event triggered when providing has been Finished
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HasFinishedProviding extends Event
{
    /**
     * @var RegistryEntry
     */
    private $entry;

    /**
     * @param RegistryEntry $entry
     */
    public function __construct(RegistryEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get entry
     *
     * @return RegistryEntry
     */
    public function getEntry()
    {
        return $this->entry;
    }
}
