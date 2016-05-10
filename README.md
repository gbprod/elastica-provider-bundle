# Elasticsearch provider bundle

Bundle that can easely provide data in Elasticsearch using [Elastica](http://eastica.io).

[![Build Status](https://travis-ci.org/gbprod/elastica-provider-bundle.svg?branch=master)](https://travis-ci.org/gbprod/elastica-provider-bundle)
[![codecov](https://codecov.io/gh/gbprod/elastica-provider-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/gbprod/elastica-provider-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gbprod/elastica-provider-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gbprod/elastica-provider-bundle/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/gbprod/elastica-provider-bundle/v/stable)](https://packagist.org/packages/gbprod/elastica-provider-bundle)
[![Total Downloads](https://poser.pugx.org/gbprod/elastica-provider-bundle/downloads)](https://packagist.org/packages/gbprod/elastica-provider-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gbprod/elastica-provider-bundle/v/unstable)](https://packagist.org/packages/gbprod/elastica-provider-bundle)
[![License](https://poser.pugx.org/gbprod/elastica-provider-bundle/license)](https://packagist.org/packages/gbprod/elastica-provider-bundle)

## Installation

Download bundle using [composer](https://getcomposer.org/) :

```bash
composer require gbprod/elastica-provider-bundle
```

Declare in your `app/AppKernel.php` file:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new GBProd\ElasticaProviderBundle\ElasticaProviderBundle(),
    );
}
```

## Usage

### Configure Elastica client

```yml
gbprod_elastica_provider:
    default_client: 'elastica.default_client' # Elastica client service's name 
```

You can create Elastica client using a bundle like:
  * [FOSElasticaBundle](https://github.com/FriendsOfSymfony/FOSElasticaBundle)  
    Service name will look like `fos_elastica.client.my_client`
  * My lightweight bundle [ElasticaBundle](https://github.com/gbprod/elastica-bundle)  
    Service name will look like `elastica.default_client`
  * DIY

### Create a Provider

```php
<?php

namespace GBProd\AcmeBundle\Provider;

use GBProd\ElasticaProviderBundle\Provider\BulkProvider;

class SuperHeroprovider extends BulkProvider
{
    protected function populate()
    {
        $this->index(
            'Spider-Man', // id of the document
            [
                "name" => "Spider-Man",
                "description" => "Bitten by a radioactive spider, high school student Peter Parker gained the speed, strength and powers of a spider. Adopting the name Spider-Man, Peter hoped to start a career using his new abilities. Taught that with great power comes great responsibility, Spidey has vowed to use his powers to help people.",
            ]
        );

        $this->update(
            'Hulk',
            [
                "name" => "Hulk",
                "description" => "Caught in a gamma bomb explosion while trying to save the life of a teenager, Dr. Bruce Banner was transformed into the incredibly powerful creature called the Hulk. An all too often misunderstood hero, the angrier the Hulk gets, the stronger the Hulk gets.",
            ]
        );

        $this->create(
            'Thor',
            [
                "name" => "Thor",
                "description" => "As the Norse God of thunder and lightning, Thor wields one of the greatest weapons ever made, the enchanted hammer Mjolnir. While others have described Thor as an over-muscled, oafish imbecile, he's quite smart and compassionate.  He's self-assured, and he would never, ever stop fighting for a worthwhile cause.",
            ]
        );

        $this->delete('Captain America');
    }

    public function count()
    {
        return 4;
    }
}
```

### Register your provider

```yml
# AcmeBundle/Resources/config/services.yml

services:
    acme_bundle.superhero_provider:
        class: GBProd\AcmeBundle\Provider\SuperHeroprovider
        tags:
            - { name: elastica.provider, index: app, type: superheros }
```

### Provide

```bash
php app/console elasticsearch:provide app superheros
```

You also can provide a full index:

```bash
php app/console elasticsearch:provide app
```

Or run all providers:

```bash
php app/console elasticsearch:provide
```

You can set a specific client to use (if not default):

```bash
php app/console elasticsearch:provide app superheros --client=client_service_name
```

### Example using doctrine

```php
<?php

namespace GBProd\AcmeBundle\Provider;

use GBProd\ElasticaProviderBundle\Provider\BulkProvider;
use Doctrine\ORM\EntityManager;

class SuperHeroprovider extends BulkProvider
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function populate()
    {
        $query = $this->em->createQuery('SELECT s FROM AcmeBundle\Model\SuperHero s');

        $results = $query->iterate();
        foreach ($results as $row) {
            $this->index(
                $row[0],
                [
                    "name" => $row[0],
                    "description" => $row[1],
                ]
            );

            $this->em->detach($row[0]);
        }
    }

    public function count()
    {
        $query = $this->em
            ->createQuery('SELECT COUNT(s.id) FROM AcmeBundle\Model\SuperHero s')
        ;

        return $query->getSingleScalarResult();
    }
}
```

```yml
# AcmeBundle/Resources/config/services.yml

services:
    acme_bundle.superhero_provider:
        class: GBProd\AcmeBundle\Provider\SuperHeroprovider
        arguments:
            - '@doctrine.orm.entity_manager'
        tags:
            - { name: elastica.provider, index: app, type: superheros }
```

### Changing bulk size

Bulk size is important when providing data to elasticsearch. Take care of your nodes setting a good bulk size.
Default bulk size is 1000, you can change setting the bulk entry of the tag.

```yml
# AcmeBundle/Resources/config/services.yml

services:
    acme_bundle.superhero_provider:
        class: GBProd\AcmeBundle\Provider\SuperHeroprovider
        calls:
            - ['changeBulkSize', 42]
        tags:
            - { name: elastica.provider, index: app, type: superheros }
```

Or directly inside a provider.
```php
<?php

namespace GBProd\AcmeBundle\Provider;

use GBProd\ElasticaProviderBundle\Provider\BulkProvider;

class SuperHeroprovider extends BulkProvider
{
    public function __construct()
    {
        $this->changeBulkSize(42);
    }

    protected function populate()
    {
        // ...
    }
}
```
### About count method

This is not mandatory to implements `count` method but it allows you to have a pretty progressbar while provider is running.

```php
<?php

namespace GBProd\AcmeBundle\Provider;

use GBProd\ElasticaProviderBundle\Provider\BulkProvider;

class SuperHeroprovider extends BulkProvider
{
    protected function populate()
    {
        // ...
    }

    public function count()
    {
        return 2;
    }
}
```
