<?php

namespace GBProd\ElasticaProviderBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event triggered when a document has been provided
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HasProvidedDocument extends Event
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
