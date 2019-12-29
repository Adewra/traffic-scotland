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
            'incidents' => new Session( new ChromeDriver('http://localhost:9222', null, 'https://trafficscotland.org/')),
            'events' => new Session( new ChromeDriver('http://localhost:9222', null, 'https://trafficscotland.org/'))
        ));

        // /Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --disable-gpu --headless --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222
    }

    public function incidents()
    {
        if($this->config['functionality']['incidents'] !== true)
            return;

        $incidents = collect();

        if($this->config['collection_methods']['api'] === true)
        {
            $browser = $this->mink->getSession('incidents');
            $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/current-incidents');
            $currentIncidents = json_decode($browser->getPage()->getText());
            foreach ($currentIncidents->layer->points as $currentIncident) {
                $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/current-incidents/' . $currentIncident->pointId);
                $incident = json_decode($browser->getPage()->getText(), true);
                $incidents->push($incident);
            }
        }
        else if($this->config['collection_methods']['rss_feeds'] === true)
        {
            $currentIncidentsFeed = Feed::make('https://trafficscotland.org/rss/feeds/currentincidents.aspx');
            $incidents = collect($currentIncidentsFeed->items)->map(function ($item, $key) {

                $incident = $item->toArray();
                $incident['latitude'] = $item->latitude;
                $incident['longitude'] = $item->longitude;
                $incident['link'] = $item->link;

                if($this->config['collection_methods']['webpage_scraping'] === true)
                {
                    /* @TODO Refator to use Mink. */

                    $client = new \Goutte\Client();
                    $client->followRedirects();

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


            if($this->config['storage'] === true)
            {
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
            }
        }
        else
        {
            print "Skipping Current Incidents as there is no suitable collection method available.";
        }

        if($this->config['storage'] === true) {
            foreach ($incidents->all() as $incident) {
                \DB::beginTransaction();
                try {
                    Incident::updateOrCreate(
                        [
                            'identifier' => $incident['incidentId'],
                            'source' => $incident['source'] // Not really required, but in keeping with the design.
                        ]
                        , $incident);
                } catch (\Exception $e) {
                    \DB::rollback();
                    throw $e;
                }
                \DB::commit();
            }
        }

        return $incidents;
    }

    public function roadworks(bool $current, bool $planned)
    {
        if($this->config['functionality']['roadworks']['current'] === false
            && $this->config['functionality']['roadworks']['planned'] === false)
            return;

        $roadworks = collect();

        if($this->config['collection_methods']['api'] === true)
        {
            if($current) {
                $browser = $this->mink->getSession('roadworks');
                $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/current-roadworks');
                $currentRoadworks = json_decode($browser->getPage()->getText());
                foreach ($currentRoadworks->layer->points as $currentRoadwork) {
                    $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/current-roadworks/' . $currentRoadwork->pointId);
                    $roadwork = json_decode($browser->getPage()->getText(), true);
                    $roadwork['description'] = $this->explodeDescription2($roadwork['description'])->toArray();
                    $roadworks->push($roadwork);
                }
            }

            if($planned) {
                $browser = $this->mink->getSession('roadworks');
                $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/planned-roadworks');
                $plannedRoadworks = json_decode($browser->getPage()->getText());
                foreach ($plannedRoadworks->layer->points as $plannedRoadwork) {
                    $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/planned-roadworks/' . $plannedRoadwork->pointId);
                    $roadwork = json_decode($browser->getPage()->getText(), true);
                    $roadwork['description'] = $this->explodeDescription2($roadwork['description'])->toArray();
                    $roadworks->push($roadwork);
                }
            }
        }
        else if($this->config['collection_methods']['rss_feeds'] === true)
        {
            $feeds = collect();
            if($current)
                $feeds->push([
                        'name' => 'current',
                        'url' => 'https://trafficscotland.org/rss/feeds/roadworks.aspx']
                );
            if($planned)
                $feeds->push([
                        'name' => 'planned',
                        'url' => 'https://trafficscotland.org/rss/feeds/plannedroadworks.aspx']
                );

            $capturedFields = ['start_date','end_date','delay_information','works','traffic_management'];
            $uncapturedFields = [];

            foreach ($feeds as $feed)
            {
                $prefixes = $this->prefixes;
                $mink = $this->mink;
                $roadworks = collect(Feed::make($feed['url'])->items)->map(function ($item) use ($feed, $capturedFields, &$uncapturedFields, $prefixes, $mink) {

                    $roadwork = $item->toArray();

                    /* Having to extrapolate identifier portion as the redirect changes the case of the parameter */
                    $roadwork['identifier'] = str_replace(array_keys($prefixes), '', str_replace('http://tscot.org/', '', $item->link));
                    $roadwork['prefix'] = str_replace($roadwork['identifier'], '', str_replace('http://tscot.org/', '', $item->link));
                    $roadwork['latitude'] = $item->latitude;
                    $roadwork['longitude'] = $item->longitude;
                    $roadwork['link'] = $item->link;

                    if($feed['name'] === 'planned') {

                        /* Ideally this should use $this->explode_description(), but it doesnt work as it is different sources */

                        $descriptionFormatted = collect(
                            explode("#",
                                str_replace("  Traffic Management:", "#Traffic Management:",
                                    implode(" ", explode("\n",
                                        str_replace('<br>', "#", $item->description)))
                                )
                            )
                        );
                        $descriptionFormatted = $descriptionFormatted->map(function ($item) {
                            list($key, $value) = explode(": ", $item);
                            return [$key => $value];
                        })->mapWithKeys(function ($item) {
                            return [snake_case(key($item)) => $item[key($item)]];
                        });
                    } else if($feed['name'] === 'current') {
                        $descriptionFormatted = collect(explode('<br>', $item->description))->map(function ($item) {
                            list($key, $value) = explode(": ", $item);
                            return [$key => $value];
                        })->mapWithKeys(function ($item) {
                            return [snake_case(key($item)) => $item[key($item)]];
                        });
                    }

                    $roadwork['start_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['start_date']);
                    $roadwork['end_date'] = Carbon::createFromFormat("l, d F Y \- H:i", $descriptionFormatted['end_date']);
                    if (isset($descriptionFormatted['delay_information']))
                        $roadwork['delay_information'] = $descriptionFormatted['delay_information'];
                    if (isset($descriptionFormatted['works']))
                        $roadwork['works'] = $descriptionFormatted['works'];
                    if (isset($descriptionFormatted['traffic_management']))
                        $roadwork['traffic_management'] = $descriptionFormatted['traffic_management'];

                    if(array_diff(array_keys($descriptionFormatted->toArray()), $capturedFields) && !str_contains($roadwork['prefix'], ['03h', '04h'])) {
                        $newUncapturedFields = array_diff(array_keys($descriptionFormatted->toArray()), $capturedFields);
                        $uncapturedFields = array_unique(array_merge($newUncapturedFields, $uncapturedFields));
                    }

                    if($this->config['collection_methods']['webpage_scraping'] === true)
                    {
                        $browser = $this->mink->getSession('roadworks');
                        if(strcasecmp($roadwork['prefix'], "03c") === 0)
                        {
                            $browser->visit('https://trafficscotland.org/roadworks/details.aspx?id=c'.$roadwork['identifier']);
                            $browser = $browser->getPage();
                            if(str_contains($browser->find('css', 'div#roadworkdetail')->getText(), "Sorry, no information is available for this roadwork."))
                                echo "Failed to load Roadwork details.";
                            else
                            {
                                $roadworkDetails = collect($browser->findAll('xpath', '//DIV[@id="roadworkdetail"]/TABLE[1]/TBODY[1]/TR[position() <= 5]'))->map(function ($node, $i) {
                                    return [$node->findAll('css', 'td')[0]->getText() => $node->findAll('css', 'td')[1]->getText()];
                                })->mapWithKeys(function ($item) {
                                    return [snake_case(str_replace(['/',':'], '', key($item))) => $item[key($item)]];
                                });
                                $roadworkDetails['description'] = $this->explodeDescription($browser->find('xpath', '//DIV[@id="roadworkdetail"]/TABLE[1]/TBODY[1]/TR[6]/TD[2]')->getHtml());

                                if(!is_null($browser->find('xpath', '//DIV[@id="roadworkdetail"]/TABLE[1]/TBODY[1]/TR[7]')))
                                {
                                    /* @TODO Add support for multiple weeks affected */

                                    $days_and_times = collect($browser->findAll('xpath', '//DIV[@id="roadworkdetail"]/TABLE[1]/TBODY[1]/TR[7]/TD[2]/TABLE[@class="daydetail"]/TBODY/TR[position() > 1]'))->map(function($tableRow){
                                        return array_map(function($info) {
                                            return $info->getText();
                                        }, $tableRow->findAll('css', 'td'));
                                    })->map(function($daytime) {
                                        return array_combine(array_map(function($key){
                                            return snake_case($key);
                                        }, ['When','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']), array_values($daytime));
                                    })->toArray();
                                    $roadworkDetails['days_affected'] = [
                                        'week_commencing' => $browser->find('xpath', '//DIV[@id="roadworkdetail"]/TABLE[1]/TBODY[1]/TR[7]/TD[1]')->getHtml() ?? '',
                                        'days_and_times' => $days_and_times ?? '',
                                    ];
                                }

                                $roadworkDetails['media_release'] = $browser->find('xpath', '//DIV[@class="main"]/H2[text()= "Media Release"]/following-sibling::p')->getText();

                                /* @TODO Finish merging $roadwork and $roadworkDetails */
                            }
                        }
                        elseif(strcasecmp($roadwork['prefix'], "04p") === 0)
                        {
                            /* @TODO Add support for web scraping planned roadworks */
                        }
                    }

                    $roadwork['locationName'] = $roadwork['title']; unset($roadwork['title']);
                    $roadwork['startDateTime'] = $roadwork['start_date']; unset($roadwork['start_date']);
                    $roadwork['endDateTime'] = $roadwork['end_date']; unset($roadwork['end_date']);
                    unset($roadwork['date']);
                    unset($roadwork['prefix']);
                    unset($roadwork['link']);

                    return $roadwork;

                });
            }

            if(count($uncapturedFields) > 0)
                echo 'Found one or more fields against Roadworks that haven\'t been captured ('.implode(',', $uncapturedFields).').';

            $roadworks = $roadworks->filter(function($roadwork) {
                return !empty($roadwork['locationName']);
            });
        }
        else
        {
            print "Skipping Roadworks as there is no suitable collection method available.";
        }

        if($this->config['storage'] === true) {
            foreach ($roadworks->all() as $roadwork) {
                \DB::beginTransaction();
                try {
                    Roadwork::updateOrCreate(
                        [
                            'identifier' => $roadwork['plannedRoadworkId'] ?? $roadwork['roadworkId'],
                            'source' => $roadwork['source']
                        ]
                        , $roadwork);
                } catch (\Exception $e) {
                    \DB::rollback();
                    throw $e;
                }
                \DB::commit();
            }
        }

        return $roadworks;
    }

    public function events()
    {
        if($this->config['functionality']['events'] !== true)
            return;

        $events = collect();
        $venues = collect();

        if($this->config['collection_methods']['api'] === true)
        {
            $browser = $this->mink->getSession('events');
            $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/planned-events');
            $currentVenues = json_decode($browser->getPage()->getText());
            foreach ($currentVenues->layer->points as $currentVenue) {
                $browser->visit('https://myapi.trafficscotland.org/v2.0/layers/planned-events/' . $currentVenue->pointId);
                $venue = json_decode($browser->getPage()->getText(), true);
                foreach ($venue['plannedEvents'] as $plannedEvent) {
                    $plannedEvent['venueId'] = $venue['venueId'];
                    $events->push($plannedEvent);
                }
                $venues->push($venue);
            }
        }
        else if($this->config['collection_methods']['webpage_scraping'] === true) {
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
                $events = collect();
                $venues = collect();
                $links = collect();

                $eventRows = collect($tableRows)->map(function ($node, $i) use ($events, $venues, &$links) {

                    $data = collect();
                    foreach ($node->findAll("css", 'td') as $td)
                        $data->push(trim(preg_replace('!\s+!', ' ', $td->getText())));
                    $row = array_combine(['start_date','end_date','name','venues'], array_values($data->filter(function ($value, $key) {
                        return $value != "";
                    })->toArray()));
                    $row['icon'] = collect($node->findAll("css", 'img[alt="Event Icon"]'))->first();
                    if (!is_null($row['icon']))
                        $row['icon'] = $row['icon']->getAttribute('src');
                    $eventLinks = $node->findAll("xpath", "//A[contains(@href, 'event.aspx')]");
                    $venueLinks = $node->findAll("xpath", "//A[contains(@href, 'venue.aspx')]");
                    foreach(array_merge($eventLinks, $venueLinks) as $link)
                    {
                        $link2 = parse_str(parse_url($link->getAttribute('href'), PHP_URL_QUERY), $linkCopy);
                        $newLink = [
                            'original' => $link->getAttribute('href'),
                            'parsed' => collect($linkCopy)
                        ];
                        $links->push($newLink);
                        $row['identifier'] = $newLink['parsed']['id'];
                    }

                    return $node;
                });

                $eventsLinks = $links->filter(function($url) { return str_contains($url['original'], ['event.aspx']); } )->toArray();
                $venuesLinks = $links->filter(function($url) { return str_contains($url['original'], ['venue.aspx']); } )->toArray();

                foreach ($venuesLinks as $venueLink)
                {
                    $venue = (new Venue())->scrape($this->mink, intval($venueLink['parsed']['id']));
                    $venues->push($venue);
                }

                foreach ($eventsLinks as $eventLink)
                {
                    $event = (new Event())->scrape($this->mink, intval($eventLink['parsed']['id']));
                    $events->push($event);
                }

            } catch (\Exception $exception) {
                dd($exception);
                throw $exception;
            }
        }
        else
        {
            print "Skipping Events as there is no suitable collection method available.";
        }

        if($this->config['storage'] === true) {
            foreach ($venues->all() as $venue) {
                \DB::beginTransaction();
                try {
                    Venue::updateOrCreate(
                        [
                            'identifier' => $venue['venueId'],
                        ]
                        , $venue);
                } catch (\Exception $e) {
                    \DB::rollback();
                    throw $e;
                }
                \DB::commit();
            }

            foreach ($events->all() as $event) {
                \DB::beginTransaction();
                try {
                    Event::updateOrCreate(
                        [
                            'identifier' => $event['plannedEventId'],
                        ]
                        , $event);
                } catch (\Exception $e) {
                    \DB::rollback();
                    throw $e;
                }
                \DB::commit();
            }
        }

        return collect(['events' => $events, 'venues' => $venues]);
    }

    private function explodeDescription($description)
    {
        return collect(array_filter(explode('<br><br>', $description), function ($element) {
                return trim($element) != ""; }
            ))
            ->map(function($info) {
                return explode(':<br>', $info);
            })
            ->mapWithKeys(function ($item) {
                try {
                    return [snake_case($item[0]) => explode('<br>', $item[1] ?? '')];
                } catch (\Exception $exception) {
                    return $item[0];
                }
            })
            ->toArray();
    }

    private function explodeDescription2($description)
    {
        $x = collect(explode("\r\n", $description))
            ->reject(function($x){return $x === "";})
            ->values();

        $keys = $x->filter(function($text) {
            return strpos($text, ":", -1);
        })->map(function($text) {
            return substr($text, 0, -1);
        });

        $trailingValues = $x->filter(function($text) {
            return strpos($text, ":", -1) === false;
        });

        $keysIndexes = $keys->keys()->values();

        $x = $keys->mapWithKeys(function ($startingKey, $startingKeyIndex) use ($keysIndexes, $trailingValues) {

            /* Long winded way of getting the index of the next key to determine the end */
            $currentKey = $keysIndexes->search($startingKeyIndex, true);
            $currentOffset = $keysIndexes->keys()->search($currentKey, true);
            $next = $keysIndexes->slice($currentOffset, 2);
            $endingKeyIndex = $next->count() < 2 ? $next->last() : null;

            $values = $trailingValues->filter(function($value, $key) use ($startingKeyIndex, $endingKeyIndex) {
                return $key > $startingKeyIndex && ($key < $endingKeyIndex || is_null($endingKeyIndex));
            })->values();

            return [snake_case($startingKey) => $values];
        });

        return $x;
    }

}