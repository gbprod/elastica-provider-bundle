<?php

namespace GBProd\ElasticaProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event triggered when handling has been started
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HasStartedHandling extends Event
{
    /**
     * @var array<RegistryEntry>
     */
    private $entries;

    /**
     * @param array<RegistryEntry> $entries
     */
    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    /**
     * Get entries
     *
     * @return array<RegistryEntry>
     */
    public function getEntries()
    {
        return $this->entries;
    }

}
