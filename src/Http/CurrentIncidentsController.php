<?php

namespace Adewra\TrafficScotland\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrentIncidentsController extends Controller
{
    /**
     * @var \Adewra\TrafficScotland\Client $client      An instance of the client executing the requests
     */
    private $client;

    public function __construct()
    {
        $this->client = new \Adewra\TrafficScotland\Client();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index()
    {
        return $this->client->currentIncidents();
    }
}