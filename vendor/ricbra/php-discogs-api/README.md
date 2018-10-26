## Discogs Api

[![Build Status](https://secure.travis-ci.org/ricbra/php-discogs-api.png)](http://travis-ci.org/ricbra/php-discogs-api)
[![Latest Stable Version](https://poser.pugx.org/ricbra/php-discogs-api/v/stable.svg)](https://packagist.org/packages/ricbra/php-discogs-api)
[![Total Downloads](https://poser.pugx.org/ricbra/php-discogs-api/downloads.png)](https://packagist.org/packages/ricbra/php-discogs-api)
[![License](https://poser.pugx.org/ricbra/php-discogs-api/license.png)](https://packagist.org/packages/ricbra/php-discogs-api)
[![Quality](https://scrutinizer-ci.com/g/ricbra/php-discogs-api/badges/quality-score.png)](https://scrutinizer-ci.com/g/ricbra/php-discogs-api/)

This library is a PHP 5.4 implementation of the [Discogs API v2.0.](http://www.discogs.com/developers/index.html)
The Discogs API is a REST-based interface. By using this library you don't have to worry about communicating with the
API: all the hard work has already be done.

This API is build upon the shoulders of a giant: [Guzzle 4.0](http://guzzle.readthedocs.org/en/latest/). This is an absolutely awesome library.

## License
This library is released under the MIT license. See the complete license in the LICENSE file.

## Installation
Start by [installing composer](http://getcomposer.org/doc/01-basic-usage.md#installation).
Next do:

    $ composer require ricbra/php-discogs-api ~1.0.0

## Requirements
PHP >=5.4.0

## Usage
Creating a new instance is as simple as:

```php
<?php
$client = Discogs\ClientFactory::factory([]);
```

### User-Agent
Discogs requires that you supply a User-Agent. You can do this easily:

```php
<?php
$client = Discogs\ClientFactory::factory([
    'defaults' => [
        'headers' => ['User-Agent' => 'your-app-name/0.1 +https://www.awesomesite.com'],
    ]
]);
```

### Throttling
Discogs doesn't like it when you hit their API at a too high connection rate. Use the <code>ThrottleSubscriber</code> to
prevent getting errors or banned:

```php
<?php

$client = Discogs\ClientFactory::factory();
$client->getHttpClient()->getEmitter()->attach(new Discogs\Subscriber\ThrottleSubscriber());

```

### OAuth
There a lot of endpoints which require OAuth. Lucky for you using Guzzle this is peanuts.

```php
<?php

$client = Discogs\ClientFactory::factory([]);
$oauth = new GuzzleHttp\Subscriber\Oauth\Oauth1([
    'consumer_key'    => $consumerKey, // from Discogs developer page
    'consumer_secret' => $consumerSecret, // from Discogs developer page
    'token'           => $token['oauth_token'], // get this using a OAuth library
    'token_secret'    => $token['oauth_token_secret'] // get this using a OAuth library
]);
$client->getHttpClient()->getEmitter()->attach($oauth);

$response = $client->search([
    'q' => 'searchstring'
]);
```

### History
Another cool plugin is the History plugin:

```php
<?php

$client = Discogs\ClientFactory::factory([]);
$history = new GuzzleHttp\Subscriber\History();
$client->getHttpClient()->getEmitter()->attach($history);

$response = $client->search([
    'q' => 'searchstring'
]);

foreach ($history as $row) {
    print (string) $row['request'];
    print (string) $row['response'];
}

```

### More info and plugins
For more information about Guzzle and its plugins checkout [the docs.](http://guzzle.readthedocs.org/en/latest/)

### Perform a search:
Per august 2014 an signed OAuth request is required for this endpoint.

```php
<?php

$response = $client->search([
    'q' => 'Meagashira'
]);
// Loop through results
foreach ($response['results'] as $result) {
    var_dump($result['title']);
}
// Pagination data
var_dump($response['pagination']);

// Dump all data
var_dump($response->toArray());

```

### Get information about a label:

```php
<?php

$label = $service->getLabel([
    'id' => 1
]);

```

### Get information about an artist:

```php
<?php

$artist = $service->getArtist([
    'id' => 1
]);

```

### Get information about a release:

```php
<?php

$release = $service->getRelease([
    'id' => 1
]);

echo $release['title']."\n";
```

### Get information about a master release:

```php
<?php

$master  = $service->getMaster([
    'id' => 1
]);

echo $master['title']."\n";
```

## Documentation
Further documentation can be found at the [Discogs API v2.0 Documentation](http://www.discogs.com/developers/index.html).

## Contributing
Implemented a missing call? PR's are welcome! 


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/ricbra/php-discogs-api/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

