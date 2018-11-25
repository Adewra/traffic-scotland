<?php
/**
 * Created by PhpStorm.
 * User: allydewar
 * Date: 14/11/2018
 * Time: 22:23
 */

namespace Adewra\TrafficScotland;

use ArandiLopez\Feed\Facades\Feed;
use Carbon\Carbon;

class Client
{
    public function currentIncidents()
    {

        $client = new \Goutte\Client();
        $client->followRedirects();

        $currentIncidentsFeed = Feed::make('https://trafficscotland.org/rss/feeds/currentincidents.aspx');
        //$title = $currentIncidentsFeed->title;
        $incidents = collect($currentIncidentsFeed->items)->map(function ($item, $key) use ($client) {

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

                /**
                 * @todo Add support for Highways England data being distributed within the Description field
                 *  See the CurrentIncidentsSeeder for an example of this data
                 */

                $incident = $item->toArray();
                $incident['latitude'] = $item->latitude;
                $incident['longitude'] = $item->longitude;
                $incident['link'] = $item->link;

                if (isset($extendedDetails)){
                    $incident['extended_details'] = $extendedDetails->all();
                    $incident['extended_details']['date'] = Carbon::parse($incident['extended_details']['date'])->toDateString();
                }
                if (isset($weatherDetails)) $incident['weather_conditions'] = $weatherDetails->all();
                return $incident;
            } catch (\Exception $exception) {
                return $item->toArray();
            }
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