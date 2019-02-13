<?php

namespace Adewra\TrafficScotland;

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
}