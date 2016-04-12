<?php

namespace Tests\GBProd\ElasticaProviderBundle\Event;

use GBProd\ElasticaProviderBundle\Event\HasProvidedDocument;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;

/**
 * Tests for HasProvidedDocument
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HasProvidedDocumentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $testedInstance = new HasProvidedDocument('id');

        $this->assertEquals('id', $testedInstance->getId());
    }
}
