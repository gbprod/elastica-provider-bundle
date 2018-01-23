<?php

namespace Tests\GBProd\ElasticaProviderBundle\Event;

use GBProd\ElasticaProviderBundle\Event\HasFinishedProviding;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;
use PHPUnit\Framework\TestCase;

/**
 * Tests for HasFinishedProviding
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasFinishedProvidingTest extends TestCase
{
    public function testConstruction()
    {
        $entry = $this
            ->getMockBuilder(RegistryEntry::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $testedInstance = new HasFinishedProviding($entry);
        
        $this->assertEquals(
            $entry,
            $testedInstance->getEntry()
        );
    }
}
