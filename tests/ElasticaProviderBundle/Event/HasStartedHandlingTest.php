<?php

namespace Tests\GBProd\ElasticaProviderBundle\Event;

use GBProd\ElasticaProviderBundle\Event\HasStartedHandling;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;

/**
 * Tests for HasStartedHandling
 * 
 * @author gbprod <contact@gb-prod.fr>
 */
class HasStartedHandlingTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $testedInstance = new HasStartedHandling(['entries']);
        
        $this->assertEquals(['entries'], $testedInstance->getEntries());
    }
}
