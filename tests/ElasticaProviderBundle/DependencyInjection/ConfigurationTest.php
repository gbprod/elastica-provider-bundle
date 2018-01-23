<?php

namespace Tests\GBProd\ElasticaProviderBundle\DependencyInjection;

use GBProd\ElasticaProviderBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * Tests for Configuration
 *
 * @author gbprod <contact@gb-prod.fr>
 */
class ConfigurationTest extends TestCase
{
    private $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration();
    }

    public function testEmptyConfig()
    {
        $processed = $this->process([]);

        $this->assertEquals(
            [
                'default_client' => null
            ], 
            $processed
        );
    }
        
    protected function process(array $config)
    {
        $processor = new Processor();
        
        return $processor->processConfiguration(
            $this->configuration,
            $config
        );
    }
    
        public function testDefaultClient()
    {
        $processed = $this->process([
            [
                'default_client' => 'my_client_service',
            ]
        ]);

        $this->assertEquals(
            [
                'default_client' => 'my_client_service'
            ], 
            $processed
        );
    }
}