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

            if($this->config['scrape_data'] == true)
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

                    if (isset($extendedDetails) && $extendedDetails->isNotEmpty()){
                        $incident['extended_details'] = $extendedDetails->all();
                        $incident['extended_details']['date'] = Carbon::parse($incident['extended_details']['date'])->toDateString();
                    }

                    $weatherDetails = collect($crawler->filter('div.bulletin-details table tr')->each(function ($node, $i) {
                        list($key, $value) = explode(": ", trim(preg_replace('/[ \t]+/', ' ', preg_replace('/\r\n/', '', (preg_replace('/\s*$^\s*/m', "", $node->text()))))), 2);
                        return array($key => $value);
                    }))->mapWithKeys(function ($item) {
                        return [snake_case(key($item)) => $item[key($item)]];
                    });

                    if (isset($weatherDetails) && $weatherDetails->isNotEmpty()){
                        $incident['weather_conditions'] = $weatherDetails->all();
                    }

                    /*
                     *  Not been seen as working yet due to change in weather conditions. typical.
                     *
                     * $weatherDetails2 = collect($crawler->filter('div.weatheralert')->each(function ($node, $i) {
                        $assortedSectionsOfText = preg_replace('//', '', preg_replace('/\r\n/', '', ( $node->text())));
                        $sanitisedSections = collect(explode("\n", $assortedSectionsOfText))
                            ->map(function($value){
                                return trim($value);
                            })
                            ->filter(function ($value, $key) {
                                return strcmp($value, 'More Detail ›') != 0 && strcmp($value, "") !== 0 && !str_contains($value, ':');
                            })
                            ->values();

                        $incident['weather_conditions2'] =  array(
                            'colour' => $sanitisedSections[0],
                            'type' => $sanitisedSections[1],
                            'status' => $sanitisedSections[2],
                            'headline' => $sanitisedSections[3],
                            'further_details' => $sanitisedSections[4]
                        );
                    }))->mapWithKeys(function ($item) {
                        return [snake_case(key($item)) => $item[key($item)]];
                    });

                    if (isset($weatherDetails2) && $weatherDetails2->isNotEmpty()){
                        $incident['weather_conditions2'] = $weatherDetails2->all();
                    }*/

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

            if(isset($incident->extended_details))
                $incident->extended_details = collect($incident['extended_details']);

            if(isset($incident->weather_conditions))
                $incident->weather_conditions = collect($incident['weather_conditions']);

            if(isset($incident->weather_conditions2))
                $incident->weather_conditions2 = collect($incident['weather_conditions2']);
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

    public function roadworks($current = true, $planned = true)
    {
        $client = new \Goutte\Client();
        $client->followRedirects();

        $roadworksFeed = Feed::make('https://trafficscotland.org/rss/feeds/roadworks.aspx');
        //$title = $currentIncidentsFeed->title;
        $roadworks = collect($roadworksFeed->items)->map(function ($item) use ($client) {

            $roadwork = $item->toArray();
            $roadwork['latitude'] = $item->latitude;
            $roadwork['longitude'] = $item->longitude;
            $roadwork['link'] = $item->link;

            $descriptionFormatted = collect(explode('<br>', $item->description))->map(function ($item) {
                list($key, $value) = explode(": ", $item);
                return [$key => $value];
            })->mapWithKeys(function ($item) {
                return [snake_case(key($item)) => $item[key($item)]];
            });

            $roadwork['start_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['start_date']);
            $roadwork['end_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['end_date']);
            if(isset($descriptionFormatted['delay_information']))
                $roadwork['delay_information'] = $descriptionFormatted['delay_information'];

            if($this->config['scrape_data'] == true) {
                try {
                    /* Having to extrapolate identifier portion as the redirect changes the case of the parameter */
                    $param = strtolower(substr(str_replace('http://tscot.org/', '', $item->link),  3));
                    $crawler = $client->request('POST', 'https://trafficscotland.org/roadworks/details.aspx?id=c'.$param, [
                        'allow_redirects' => true
                    ]);

                    if(strcasecmp($crawler->filter('div#roadworkdetail')->first()->text(),
                        "Sorry, no information is available for this roadwork.") !== 0)
                    {
                        $roadworkDetails = collect($crawler->filter('div#roadworkdetail table tr')->each(function ($node, $i) {
                            list($key, $value) = explode(": ", trim(preg_replace('!\s+!', ' ', $node->text())), 2);
                            //dd(array($key => $value));
                            return array($key => $value);
                        }))->mapWithKeys(function ($item) {
                            return [snake_case(key($item)) => $item[key($item)]];
                        });
                        //dd($roadworkDetails);
                        $roadwork['extended_details'] = $roadworkDetails;
                        $roadwork['media_release'] = null;
                    }
                }
                catch(\Exception $exception)
                {
                    dd($exception);
                    throw $exception;
                }
            }

            return $roadwork;

        })->mapInto(Roadwork::class);

        foreach ($roadworks->all() as $roadwork) {
            \DB::beginTransaction();
            try {
                $roadwork->save();
            } catch (\Exception $e) {
                \DB::rollback();
                throw $e;
            }
            \DB::commit();
        }

        return $roadworks;
    }
}