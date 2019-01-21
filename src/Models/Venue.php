<?php

namespace Adewra\TrafficScotland;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venue';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'name',
        'address',
        'city',
        'postcode',
        'telephone',
        'website',
        'crowd_capacity',
        'travel_details'
        ];

    protected $casts = [
    ];

    protected $hidden = [
    ];

    protected $appends = [
    ];

    protected $dates = [
    ];
}