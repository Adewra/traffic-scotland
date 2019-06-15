<?php

namespace Adewra\TrafficScotland;

use Behat\Mink\Mink;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'name',
        'address',
        'city',
        'postcode',
        'link',
        'telephone',
        'website',
        'crowd_capacity'
        ];

    protected $casts = [
    ];

    protected $hidden = [
    ];

    protected $appends = [
    ];

    protected $dates = [
    ];

    public function scrape(Mink $mink, $identifier = null) : ?Venue
    {
        if(is_int($identifier))
            $this->attributes['identifier'] = $identifier;

        if(!is_null($this->identifier))
        {
            $link = 'https://trafficscotland.org/plannedevents/venue.aspx?id='.$this->attributes['identifier'];
            $browser = $mink->getSession('events');
            $browser->visit($link);
            $browser = $browser->getPage();

            $venueDetails = collect($browser->findAll("css", "table#cphMain_cmpVenueDetails_tblData tr"))->map(function ($node, $i)  {
                list($key, $value) = explode(": ", trim(preg_replace('!\s+!', ' ', $node->getText())), 2);
                return array($key => $value);
            })->mapWithKeys(function ($item) {
                return [snake_case(key($item)) => $item[key($item)]];
            });

            return $this->updateOrCreate(
                [
                    'identifier' => $identifier,
                ],
                [
                    'name' => $venueDetails['venue_name'],
                    'address' => $venueDetails['address'] ?? null,
                    'city' => $venueDetails['city'],
                    'postcode' => $venueDetails['post_code'],
                    'link' => $link,
                    'telephone' => $venueDetails['telephone'] ?? null,
                    'email' => $venueDetails['email'] ?? null,
                    'website' => $venueDetails['web_address'] ?? null,
                    'crowd_capacity' => $venueDetails['crowd_capacity'] ?? null
                ]);
        }

        return null;
    }
}