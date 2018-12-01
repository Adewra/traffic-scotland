Laravel Traffic Scotland
===============

A package for retrieving the latest traffic information in Scotland through [Traffic Scotland](https://trafficscotland.org/).

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
composer require adewra/trafficscotland
```

### Laravel version Compatibility

 Laravel  | Package
:---------|:----------
 4.2.x    | Untested
 5.0.x    | Untested
 5.1.x    | Untested
 5.2.x    | Untested
 5.3.x    | Untested
 5.4.x    | Untested
 5.5.x    | Untested
 5.6.x    | Untested
 5.7.x    | Test Build

And add the service provider in `config/app.php`:

```php
Adewra\TrafficScotland\TrafficScotlandServiceProvider::class,
```

This package hasn't been tested with [Lumen](http://lumen.laravel.com).

Upgrading
---------

Package hasn't been baselined yet.

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

Here is an example configuration:

```php
'trafficscotland' => [
    'scrape_data' => env('TRAFFICSCOTLAND_SCRAPE_DATA', false),
]]
```

To run our database migrations that allow for storing of the data:

```
php artisan migrate --path=/packages/adewra/trafficscotland/src/migrations
```


Troubleshooting
-------

Troubleshooting hasn't been written yet.

Package Features
-------------

 Type  | Included
:---------|:----------
 Configuration    | Yes
 Views    | No
 Blade Directives    | No
 Commands    | Yes
 Migrations    | Yes
 Translations    | No
 Middleware    | No
 Events   | No
 Seeds    | Yes
 
Minimum Viable Product
-------- 
- [x] Current Incidents
- [x] Roadworks
- [x] Traffic Status
- [ ] Live Traffic Cameras
- [ ] Park and Ride
- [ ] Bridge Wind Restrictions Forecast
- [ ] Police Travel Warnings
- [ ] Variable Message Signs
- [ ] Highways England
- [ ] News
- [x] Events
- [ ] Gritters
- [x] Region Filtering
- [x] GIS Features

Examples
-------- 
 
 **Retrieving Current Incidents**
 
 ```php
 $incidents = TrafficScotland::currentIncidents();
 ```
 ```
 php artisan trafficscotland:currentincidents
 ```
 ```
 php artisan tinker
 >>> Adewra\TrafficScotland\Incident::all();
 ```
