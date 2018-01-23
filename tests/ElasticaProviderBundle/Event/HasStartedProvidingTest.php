<?php

namespace Tests\GBProd\ElasticaProviderBundle\Event;

use GBProd\ElasticaProviderBundle\Event\HasStartedProviding;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;
use PHPUnit\Framework\TestCase;

/**
 * Tests for HasStartedProviding
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasStartedProvidingTest extends TestCase
{
    public function testConstruction()
    {
        $entry = $this
            ->getMockBuilder(RegistryEntry::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $testedInstance = new HasStartedProviding($entry);
        
        $this->assertEquals(
            $entry,
            $testedInstance->getEntry()
        );
    }
}
