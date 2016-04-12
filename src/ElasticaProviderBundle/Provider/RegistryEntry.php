<?php

namespace GBProd\ElasticaProviderBundle\Provider;

/**
 * Entry for provider registry
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class RegistryEntry
{
    /**
     * @var provider
     */
    private $provider;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * @param provider $provider
     * @param string       $index
     * @param string       $type
     */
    public function __construct(provider $provider, $index, $type)
    {
        $this->provider = $provider;
        $this->index    = $index;
        $this->type     = $type;
    }

    /**
     * Get provider
     *
     * @return provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Get index
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function match($index, $type)
    {
        return ($this->getIndex() == $index && $this->getType() == $type)
            || ($this->getIndex() == $index && $type === null)
            || (null === $index && $type === null)
        ;
    }
}
