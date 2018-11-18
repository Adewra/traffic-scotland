<?php
/**
 * Created by PhpStorm.
 * User: allydewar
 * Date: 14/11/2018
 * Time: 22:23
 */

namespace Adewra\TrafficScotland;

use ArandiLopez\Feed\Facades\Feed;

class Client
{
    public function currentIncidents()
    {
        $client = new \Goutte\Client();
        $client->followRedirects();

        $currentIncidentsFeed = Feed::make('https://trafficscotland.org/rss/feeds/currentincidents.aspx');
        //$title = $currentIncidentsFeed->title;
        $scrapedInformation = collect($currentIncidentsFeed->items)->map(function ($item, $key) use ($client) {

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
                $incident = $item->toArray();
                $incident['information'] = $extendedDetails;
                return $incident;
            } catch (\Exception $exception) {
                return $item->toArray();
            }
        });

        $incidents = $scrapedInformation->mapInto(\Adewra\TrafficScotland\Incident::class)
                                        ->each(function ($incident) {
                                            $this->attributes['permalink'] = $incident->permalink;
                                            $this->attributes['latitude'] = 55.8591;
                                            $this->attributes['longitude'] = 4.2581;
                                        });

        $incidents->each(function ($incident) {
            $incident->save();
        });

        return $incidents->all();
    }
}