<?php

namespace Adewra\TrafficScotland;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Roadwork extends Model
{
    protected $table = 'roadworks';
    protected $primaryKey = 'id';
    protected $increments = true;

    protected $fillable = [
        'identifier',
        'source',
        'locationName',
        'description',
        'whenType',
        'weekDays',
        'extraLocationDetails',
        'locationX',
        'locationY',
        'endDateTime',
        'startDateTime',
        'weekCommencing',
        'directionText',
        'pressReleaseText',
        'latitude',
        'longitude',
        'isOnHomePage',
        'affectedWeeks'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'description' => 'array',
        'affectedWeeks' => 'array',
        'isOnHomePage' => 'boolean'
    ];

    protected $hidden = [
        'latitude',
        'longitude'
    ];

    /*protected $appends = [
        'location'
    ];*/

    protected $dates = [
        'date',
        'start_date',
        'end_date'
    ];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value)->toDateString();
    }
}