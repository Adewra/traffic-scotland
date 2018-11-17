<?php
/**
 * Created by PhpStorm.
 * User: allydewar
 * Date: 14/11/2018
 * Time: 22:22
 */

namespace Adewra\TrafficScotland;

use Illuminate\Support\Facades\Facade;

class TrafficScotlandFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'adewra-trafficscotland';
    }
}