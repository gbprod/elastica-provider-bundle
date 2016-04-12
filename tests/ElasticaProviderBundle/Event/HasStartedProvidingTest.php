<?php

namespace Tests\GBProd\ElasticaProviderBundle\Event;

use GBProd\ElasticaProviderBundle\Event\HasStartedProviding;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;

/**
 * Tests for HasStartedProviding
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasStartedProvidingTest extends \PHPUnit_Framework_TestCase
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
