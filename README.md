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

Test's haven't been written yet.

Troubleshooting
-------

Troubleshooting hasn't been written yet.

Configuration
-------------

To publish the configuration file run:

```
> php artisan vendor:publish
```

This will copy the configuration file to `config/trafficscotland.php`.

Here is an example configuration:

```php
'trafficscotland' => [
    'method' => env('TRAFFICSCOTLAND_METHOD','rss'),
]
```

To run our database migrations that allow for storing of the data:

```
php artisan migrate --path=/packages/adewra/trafficscotland/src/migrations
```

Features
-------------

 Type  | Included
:---------|:----------
 Configuration    | Yes
 Views    | No
 Blade Directives    | No
 Commands    | No
 Migrations    | No
 Translations    | No
 Middleware    | No
 Events   | No
 
Examples
-------- 
 
 **Retrieving Current Incidents**
 
 ```php
 $incidents = TrafficScotland::currentIncidents();
 ```