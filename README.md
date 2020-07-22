Traffic Scotland
===============

A package for retrieving the latest traffic information in Scotland through [Traffic Scotland](https://trafficscotland.org/).

Designed primarily for the Laravel & Lumen frameworks, you can easily just drop this into any PHP project using Composer.

Table of contents
-----------------
* [Installation](#installation)
* [Upgrading](#upgrading)
* [Testing](#testing)
* [Configuration](#configuration)
* [Troubleshooting](#troubleshooting)
* [Package Features](#troubleshooting)
* [Minimum Viable Product](#minimumviableproduct)
* [Examples](#examples)

Installation
------------

Installation using composer:

```
composer require adewra/traffic-scotland
```

### Laravel/Lumen version compatibility

 Version  | Status
:---------|:----------
 4.x.x    | Untested
 5.x.x    | Untested
 6.x.x    | Untested
 7.0      | Test Build

For Lumen and Laravel versions earlier than `5.x` you'll also need to add the _service provider_ in `config/app.php`:

```php
Adewra\TrafficScotland\TrafficScotlandServiceProvider::class,
```

Upgrading
---------

This package is still in `Pre-release`, upgrade instructions will come when the first update is officially released.

Testing
-------

```
./vendor/bin/phpunit
```

Configuration
-------------

To publish the configuration file run:

```
> php artisan vendor:publish --tag=config --tag=migrations --tag=seeds
```

This will copy the configuration file to `config/trafficscotland.php`.

Here is an example (and the default) configuration:

```php
    'functionality' => [
        'incidents' => true,
        'roadworks' => [
            'current' => true,
            'planned' => true,
        ],
        'events' => true,
    ],
    'collection_methods' => [
        'api' => true,
        'rss_feeds' => false,
        'webpage_scraping' => false,
    ],
    'storage' => true
```

To run our database migrations that allow for storing of the data:

```
php artisan migrate --path=/packages/adewra/trafficscotland/src/migrations
```


Troubleshooting
-------

Troubleshooting hasn't been written yet.

Package features
-------------

 Type             | Included
:-----------------|:----------
 Configuration    | Yes
 Views            | No
 Blade Directives | No
 Commands         | Yes
 Migrations       | Yes
 Translations     | No
 Middleware       | No
 Events           | No
 Seeds            | Yes
 
MVP & potential future functionality
-------- 
- [x] Incidents
- [x] Roadworks (Current & Planned)
- [x] Events (& Venues)
- [ ] ~~Status/Congestion~~
- [ ] Traffic Cameras
- [ ] Queues
- [ ] Park and Ride
- [ ] Bulletins
- [ ] Weather Incidents
- [ ] Weather Stations
- [ ] Police Travel Warnings
- [ ] Variable Message Signs
- [ ] Highways England
- [ ] News
- [ ] Gritters
 
Methods
--------

The preferred method for obtaining information is through Traffic Scotland's API that is utilised by the mobile version of their website.

Alternative methods include use of Datex II Service, RSS Feeds or through scraping the web pages.

Examples
-------- 
 
 **Retrieving Current Incidents**
  
  ```php
    $incidents = TrafficScotland::incidents();
  ```
  ```
    php artisan trafficscotland:incidents
  ```
  ```
    php artisan tinker
    >>> Adewra\TrafficScotland\Incident::all();
  ```
 
  **Retrieving Roadworks**
  
   ```php
    $roadworks = TrafficScotland::roadworks(true, true);
   ```
   ```
    php artisan trafficscotland:roadworks
   ```
   ```
    php artisan tinker
    >>> Adewra\TrafficScotland\Roadwork::all();
   ```
   
 **Retrieving Events**
 
  ```php
   $roadworks = TrafficScotland::events();
  ```
  ```
   php artisan trafficscotland:events
  ```
  ```
   php artisan tinker
   >>> Adewra\TrafficScotland\Event::all();
   >>> Adewra\TrafficScotland\Venue::all();
  ```
   
   Lessons Learned
   -------- 
   
   - My original choice of HTTP client Goutte isn't able to handle Traffic Scotland's website's javascript based navigation and as such, I should have gone with Selenium 2 from the beginning. Using Behat's Mink I am able to easily switch between both.
