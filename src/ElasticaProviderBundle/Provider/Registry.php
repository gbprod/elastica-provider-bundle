<?php

namespace GBProd\ElasticaProviderBundle\Provider;

/**
 * Registry for provider
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class Registry
{
    /**
     * @var array<RegistryEntry>
     */
    private $entries = [];

    /**
     * Add a entry to the registry
     *
     * @param RegistryEntry $entry
     */
    public function add(RegistryEntry $entry)
    {
        $this->entries[] = $entry;

        return $this;
    }

    /**
     * Get entries for index and type
     *
     * @param string|null $index
     * @param string|null $type
     *
     * @return array<ProviderEntry>
     */
    public function get($index = null, $type = null)
    {
        return array_filter(
            $this->entries,
            function($entry) use ($index, $type) {
                return $entry->match($index, $type);
            }
        );
    }
}