<?php

namespace Adewra\TrafficScotland;

use ArandiLopez\Feed\Facades\Feed;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Carbon\Carbon;
use DMore\ChromeDriver\ChromeDriver;

class Client
{
    private $config = [];
    protected $mink;

    public function __construct()
    {
        $this->config = config('trafficscotland');
        $this->mink = new Mink(array(
            'roadworks' => new Session( new ChromeDriver('http://localhost:9222', null, 'https://trafficscotland.org/')),
            'events' => new Session( new ChromeDriver('http://localhost:9222', null, 'https://trafficscotland.org/'))
        ));

        // /Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --disable-gpu --headless --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222
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

    public function roadworks(bool $current = true, bool $planned = true)
    {
        $client = new \Goutte\Client();
        $client->followRedirects();


        $roadworks = collect();
        $currentRoadworks = collect();
        $plannedRoadworks = collect();

        if($current === true) {
            $currentRoadworksFeed = Feed::make('https://trafficscotland.org/rss/feeds/roadworks.aspx');
            $currentRoadworks = collect($currentRoadworksFeed->items)->map(function ($item) use ($client) {

                $currentRoadwork = $item->toArray();
                $currentRoadwork['latitude'] = $item->latitude;
                $currentRoadwork['longitude'] = $item->longitude;
                $currentRoadwork['link'] = $item->link;

                $descriptionFormatted = collect(explode('<br>', $item->description))->map(function ($item) {
                    list($key, $value) = explode(": ", $item);
                    return [$key => $value];
                })->mapWithKeys(function ($item) {
                    return [snake_case(key($item)) => $item[key($item)]];
                });

                $currentRoadwork['start_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['start_date']);
                $currentRoadwork['end_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['end_date']);
                if (isset($descriptionFormatted['delay_information']))
                    $currentRoadwork['delay_information'] = $descriptionFormatted['delay_information'];

                if ($this->config['scrape_data'] == true) {
                    try {
                        /* Having to extrapolate identifier portion as the redirect changes the case of the parameter */
                        $param = strtolower(substr(str_replace('http://tscot.org/', '', $item->link), 3));
                        $crawler = $client->request('POST', 'https://trafficscotland.org/roadworks/details.aspx?id=c' . $param, [
                            'allow_redirects' => true
                        ]);

                        if (strcasecmp($crawler->filter('div#roadworkdetail')->first()->text(),
                                "Sorry, no information is available for this roadwork.") !== 0) {
                            $roadworkDetails = collect($crawler->filter('div#roadworkdetail table tr')->each(function ($node, $i) {
                                list($key, $value) = explode(": ", trim(preg_replace('!\s+!', ' ', $node->text())), 2);
                                return array($key => $value);
                            }))->mapWithKeys(function ($item) {
                                return [snake_case(key($item)) => $item[key($item)]];
                            });
                            $currentRoadwork['extended_details'] = $roadworkDetails;
                            $currentRoadwork['media_release'] = null;
                        }
                    } catch (\Exception $exception) {
                        dd($exception);
                        throw $exception;
                    }
                }

                return $currentRoadwork;

            })->mapInto(Roadwork::class);
        }

        if($planned === true) {
            $plannedRoadworksFeed = Feed::make('https://trafficscotland.org/rss/feeds/plannedroadworks.aspx');
            $plannedRoadworks = collect($plannedRoadworksFeed->items)->map(function ($item) use ($client) {

                $plannedRoadwork = $item->toArray();
                $plannedRoadwork['latitude'] = $item->latitude;
                $plannedRoadwork['longitude'] = $item->longitude;
                $plannedRoadwork['link'] = $item->link;

                $descriptionFormatted = collect(explode("#", str_replace("  Traffic Management:", "#Traffic Management:", implode(" ", explode("\n", str_replace('<br>',"#", $item->description))))));

                $descriptionFormatted = $descriptionFormatted->map(function ($item) {
                    list($key, $value) = explode(": ", $item);
                    return [$key => $value];
                })->mapWithKeys(function ($item) {
                    return [snake_case(key($item)) => $item[key($item)]];
                });

                $plannedRoadwork['start_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['start_date']);
                $plannedRoadwork['end_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['end_date']);
                if (isset($descriptionFormatted['works']))
                    $plannedRoadwork['works'] = $descriptionFormatted['works'];
                if (isset($descriptionFormatted['traffic_management']))
                    $plannedRoadwork['traffic_management'] = $descriptionFormatted['traffic_management'];

                //dd($plannedRoadwork);

                if ($this->config['scrape_data'] == true) {
                    try {

                    } catch (\Exception $exception) {
                        dd($exception);
                        throw $exception;
                    }
                }

                return $plannedRoadwork;

            })->mapInto(Roadwork::class);
        }

        $roadworks = $currentRoadworks->merge($plannedRoadworks);

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

    public function events()
    {
        $events = collect();
        $venues = collect();

        if ($this->config['scrape_data'] == true) {
            try {
                $browser = $this->mink->getSession('events');
                $browser->visit('https://trafficscotland.org/plannedevents/index.aspx');
                $browser = $browser->getPage();

                $showAllSelect = $browser->find('css', 'select#cphMain_pevents_cmpPlannedEventList_ddlPaging');
                $showAllSelect->selectOption('Show All');
                $pagingGo = $browser->find('css', 'input#cphMain_pevents_cmpPlannedEventList_btnChangePaging');
                $pagingGo->click();

                $tableRows = $browser->findAll("css", 'table.infogrid tbody tr');
                /**
                 * Below was to capture items not on the detail page, such as the iconography.
                 */
                /*$eventRows = collect($tableRows)->map(function ($node, $i) use ($events, $venues, $mink) {

                    $data = collect();
                    foreach ($node->findAll("css", 'td') as $td)
                        $data->push(trim(preg_replace('!\s+!', ' ', $td->getText())));
                    $row = $data->filter(function ($value, $key) {
                        return $value != "";
                    })->toArray();
                    $row[5] = collect($node->findAll("css", 'img[alt="Event Icon"]'))->first();
                    if (!is_null($row[5]))
                        $row[5] = $row[5]->getAttribute('src');

                    return $row;
                });*/

                $events = collect();
                $venues = collect();

                $links = collect($tableRows)->map(function ($row) {
                    $links = collect();
                    foreach($row->findAll("css", 'td.l a') as $link)
                        $links->push($link->getAttribute('href'));
                    return $links->filter(function ($value, $key) {
                        return $value != null;
                    });
                })->flatten();

                $eventsLinks = $links->filter(function($url) { return str_contains($url, ['event.aspx']); } )->toArray();
                $venuesLinks = $links->filter(function($url) { return str_contains($url, ['venue.aspx']); } )->toArray();

                foreach ($venuesLinks as $venuesLink)
                {
                    $identifier = intval(str_replace("venue.aspx?id=", "", $venuesLink));
                    $venue = (new Venue())->scrape($mink, $identifier);
                    $venues->push($venue);
                }

                foreach ($eventsLinks as $eventLink)
                {
                    $identifier = intval(str_replace("event.aspx?id=", "", $eventLink));
                    $event = (new Event())->scrape($mink, $identifier);
                    $events->push($event);
                }

            } catch (\Exception $exception) {
                dd($exception);
                throw $exception;
            }
        }

        /*if($this->attributes['persist'] == true)
        {*/
            foreach ($venues->all() as $venue) {
                \DB::beginTransaction();
                try {
                    $venue->save();
                } catch (\Exception $e) {
                    \DB::rollback();
                    throw $e;
                }
                \DB::commit();
            }

            foreach ($events->all() as $event) {
                \DB::beginTransaction();
                try {
                    $event->save();
                } catch (\Exception $e) {
                    \DB::rollback();
                    throw $e;
                }
                \DB::commit();
            }
        /*/}*/

        return collect(['events' => $events, 'venues' => $venues]);
    }



}