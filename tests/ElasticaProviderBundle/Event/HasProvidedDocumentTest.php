<?php

namespace Tests\GBProd\ElasticaProviderBundle\Event;

use GBProd\ElasticaProviderBundle\Event\HasProvidedDocument;
use GBProd\ElasticaProviderBundle\Provider\RegistryEntry;
use PHPUnit\Framework\TestCase;

/**
 * Tests for HasProvidedDocument
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class HasProvidedDocumentTest extends TestCase
{
    public function testConstruction()
    {
        $testedInstance = new HasProvidedDocument('id');

        $this->assertEquals('id', $testedInstance->getId());
    }
}
