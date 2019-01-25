<?php

namespace Adewra\TrafficScotland;

use Illuminate\Support\Facades\Facade;

class TrafficScotlandFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'adewra-trafficscotland';
    }
}