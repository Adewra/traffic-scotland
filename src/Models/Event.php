<?php

namespace Adewra\TrafficScotland;

use Behat\Mink\Mink;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'name',
        'date',
        'start_time',
        'end_time',
        'link',
        'icon',
        'description',
        'historic_attendance',
        'last_updated_by_provider',
        'venue_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'last_updated_by_provider' => 'datetime'
    ];

    protected $hidden = [
    ];

    protected $appends = [
    ];

    protected $dates = [
        'date',
        'start_time',
        'end_time',
        'last_updated_by_provider'
    ];

    public function scrape(Mink $mink, $identifier = null, $extra = [])
    {
        if(is_int($identifier))
            $this->attributes['identifier'] = $identifier;

        if(isset($this->attributes['identifier']))
        {
            $link = 'https://trafficscotland.org/plannedevents/event.aspx?id='.$this->attributes['identifier'];
            $browser = $mink->getSession('browser');
            $browser->visit($link);
            $browser = $browser->getPage();

            $eventDetails = collect($browser->findAll("css", "table#cphMain_cmpPlannedEventDetails_tblData tr"))->map(function ($node, $i) {
                list($key, $value) = explode(": ", trim(preg_replace('!\s+!', ' ', $node->getText())), 2);
                return array($key => $value);
            })->mapWithKeys(function ($item) {
                return [snake_case(key($item)) => $item[key($item)]];
            });

            if(!isset($eventDetails['name']))
                $eventDetails['name'] = $browser->find("css", "div.main h1")->getText();

            $eventDetails = $eventDetails->toArray();

            return $this->firstOrNew(
                [
                    'identifier' => $identifier,
                ],
                [
                    'name' => $eventDetails['name'],
                    'date' => Carbon::createFromFormat("l\, d F Y", $eventDetails['date']),
                    'start_time' => Carbon::createFromFormat("d M y \- H:i", $eventDetails['start_time'], "Europe/London"),
                    'end_time' => Carbon::createFromFormat("d M y \- H:i", $eventDetails['end_time'], "Europe/London"),
                    'link' => $link,
                    'icon' => '',
                    //'venue_id' => null,
                    'description' => $eventDetails['description'],
                    'historic_attendance' => $eventDetails['historic_attendance'] ?? null,
                    'last_updated_by_provider' => Carbon::createFromFormat("d M y \- H:i", $eventDetails['last_updated_by_provider'], "Europe/London") ?? null
                ]);
        }

        return null;
    }
}