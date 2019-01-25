<?php

namespace Adewra\TrafficScotland\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoadworksController extends Controller
{
    /**
     * @var \Adewra\TrafficScotland\Client $client      An instance of the client executing the requests
     */
    private $client;

    private $currentRoadworks;
    private $plannedRoadworks;

    public function __construct()
    {
        $this->client = new \Adewra\TrafficScotland\Client();
        $this->currentRoadworks = collect();
        $this->plannedRoadworks = collect();
    }

    /**
     * Display a listing of the resource.
     *
     * @param $current boolean Current roadworks
     * @param $planned boolean Planned roadworks
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index($current = true, $planned = true)
    {
        $roadworks = collect();

        if($current == true)
            $this->currentRoadworks = $this->client->roadworks(true, false);

        if($planned == true)
            $this->plannedRoadworks = $this->client->roadworks(false, true);

        if($this->currentRoadworks->count() > 0)
            $roadworks->merge($this->currentRoadworks);

        if($this->plannedRoadworks->count() > 0)
            $roadworks->merge($this->plannedRoadworks);

        return $roadworks;
    }
}