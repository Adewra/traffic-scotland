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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index()
    {
        $this->currentRoadworks = collect();
        //$this->currentRoadworks = $this->client->roadworks(true, false);
        $this->plannedRoadworks = $this->client->roadworks(false, true);

        if($this->currentRoadworks->count() > 0)
        {
            if($this->plannedRoadworks->count() > 0)
                return $this->currentRoadworks->merge($this->plannedRoadworks);
            else
                return $this->currentRoadworks;
        }
        else if($this->plannedRoadworks->count() > 0)
            return $this->plannedRoadworks;

        return collect();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}