<?php

namespace GBProd\ElasticaProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;

/**
 * Event triggered when providing has been started
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HasStartedProviding extends Event
{
    private $entry;

    public function __construct(RegistryEntry $entry)
    {
        $this->entry = $entry;
    }

    public function getEntry()
    {
        return $this->entry;
    }
}
