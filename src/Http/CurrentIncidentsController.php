<?php
/**
 * Created by PhpStorm.
 * User: allydewar
 * Date: 14/11/2018
 * Time: 22:27
 */

namespace Adewra\TrafficScotland\Http;

use Adewra\TrafficScotland\Client;
use Adewra\TrafficScotland\TrafficScotlandFacade;
use Illuminate\Routing\Controller as BaseController;

class CurrentIncidentsController extends BaseController
{
    public function index()
    {
        //if(strcasecmp(config('trafficscotland.method'), 'rss') == 0)
        //{
            $client = new Client();
            return $client->currentIncidents();
        //}
        //else
            //throw new \Exception("Unknown method `".config('trafficscotland')."` for collecting Traffic Scotland data.");
    }
}