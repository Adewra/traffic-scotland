<?php

namespace Adewra\TrafficScotland;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\Selenium2Driver;
use Illuminate\Support\Str;

class Client
{
    private $config = [];
    protected $mink;

    public function __construct()
    {
        $this->config = config('trafficscotland');

        $this->mink = new Mink(array(
            'roadworks' => new Session( new Selenium2Driver('firefox', null, $this->config['selenium_webdriver'])),
            'incidents' => new Session( new Selenium2Driver('firefox', null, $this->config['selenium_webdriver'])),
            'events' => new Session( new Selenium2Driver('firefox', null, $this->config['selenium_webdriver']))
        ));
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
                    return [Str::snake($item[0]) => explode('<br>', $item[1] ?? '')];
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

            return [Str::snake($startingKey) => $values];
        });

        return $x;
    }

}