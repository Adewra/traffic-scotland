<?php

namespace Adewra\TrafficScotland;

use ArandiLopez\Feed\Facades\Feed;
use Carbon\Carbon;

class Client
{
    private $config = [];

    public function __construct()
    {
        $this->config = config('trafficscotland');
    }

    public function currentIncidents()
    {
        $client = new \Goutte\Client();
        $client->followRedirects();

        $currentIncidentsFeed = Feed::make('https://trafficscotland.org/rss/feeds/currentincidents.aspx');
        //$title = $currentIncidentsFeed->title;
        $incidents = collect($currentIncidentsFeed->items)->map(function ($item, $key) use ($client) {

            $incident = $item->toArray();
            $incident['latitude'] = $item->latitude;
            $incident['longitude'] = $item->longitude;
            $incident['link'] = $item->link;

            if($this->config['capture_extended_data'] == true)
            {
                try {
                    $crawler = $client->request('POST', $item->link, [
                        'allow_redirects' => true
                    ]);
                    $extendedDetails = collect($crawler->filter('div#incidentdetail table tr')->each(function ($node, $i) {
                        list($key, $value) = explode(": ", trim(preg_replace('!\s+!', ' ', $node->text())), 2);
                        return array($key => $value);
                    }))->mapWithKeys(function ($item) {
                        return [snake_case(key($item)) => $item[key($item)]];
                    });

                    $weatherDetails = collect($crawler->filter('div.bulletin-details table tr')->each(function ($node, $i) {
                        list($key, $value) = explode(": ", trim(preg_replace('/[ \t]+/', ' ', preg_replace('/\r\n/', '', (preg_replace('/\s*$^\s*/m', "", $node->text()))))), 2);
                        return array($key => $value);
                    }))->mapWithKeys(function ($item) {
                        return [snake_case(key($item)) => $item[key($item)]];
                    });

                    if (isset($extendedDetails)){
                        $incident['extended_details'] = $extendedDetails->all();
                        $incident['extended_details']['date'] = Carbon::parse($incident['extended_details']['date'])->toDateString();
                    }
                    if (isset($weatherDetails)) $incident['weather_conditions'] = $weatherDetails->all();

                } catch (\Exception $exception) {
                    return $incident;
                }
            }

            /**
             * @todo Add support for Highways England data being distributed within the Description field
             *  See the CurrentIncidentsSeeder for an example of this data
             */

            return $incident;

        })->mapInto(Incident::class);

        $incidents->each(function($incident) {
            if(isset($incident->weather_conditions)) {
                $incident->weather_conditions = collect($incident->weather_conditions);
            }

            if(isset($incident->extended_details))
                $incident->extended_details = collect($incident->extended_details);
        });

        foreach($incidents->all() as $incident)
        {
            \DB::beginTransaction();
            try {
                $incident->save();
            }
            catch(\Exception $e)
            {
                \DB::rollback();
                throw $e;
            }
            \DB::commit();
        }

        return $incidents;
    }
}