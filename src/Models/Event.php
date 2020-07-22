<?php

namespace Adewra\TrafficScotland;

use Behat\Mink\Mink;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'startDateTime',
        'endDateTime',
        'description',
        'historicAttendance',
        'isCancelled',
        'isCurrent',
        'lastUpdated',
        'name',
        'venueId'
    ];

    protected $casts = [
        'startDateTime' => 'datetime',
        'endDateTime' => 'datetime',
        'lastUpdated' => 'datetime'
    ];

    protected $hidden = [
    ];

    protected $appends = [
    ];

    protected $dates = [
        'startDateTime',
        'endDateTime',
        'lastUpdated'
    ];

    public function setStartDateTimeAttribute($value)
    {
        $this->attributes['startDateTime'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setEndDateTimeAttribute($value)
    {
        $this->attributes['endDateTime'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setLastUpdatedAttribute($value)
    {
        $this->attributes['lastUpdated'] = Carbon::parse($value)->toDateTimeString();
    }

    public function fetch(Mink $mink, $identifier = null)
    {
        /* Unable to decouple, no separate API call */
    }

    public function scrape(Mink $mink, $identifier = null, $extra = [])
    {
        if(is_int($identifier))
            $this->attributes['identifier'] = $identifier;

        if(!is_null($this->identifier))
        {
            $link = 'https://trafficscotland.org/plannedevents/event.aspx?id='.$this->attributes['identifier'];
            $browser = $mink->getSession('events');
            $browser->visit($link);
            $browser = $browser->getPage();

            $eventDetails = collect($browser->findAll("css", "table#cphMain_cmpPlannedEventDetails_tblData tr"))->map(function ($node, $i) {
                list($key, $value) = explode(": ", trim(preg_replace('!\s+!', ' ', $node->getText())), 2);
                return array($key => $value);
            })->mapWithKeys(function ($item) {
                return [Str::snake(key($item)) => $item[key($item)]];
            });

            if(!isset($eventDetails['name']))
                $eventDetails['name'] = $browser->find("css", "div.main h1")->getText();

            $eventDetails = $eventDetails->toArray();

            return $this->updateOrCreate(
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