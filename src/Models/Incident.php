<?php
/**
 * Created by PhpStorm.
 * User: allydewar
 * Date: 14/11/2018
 * Time: 23:21
 */

namespace Adewra\TrafficScotland;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';

    protected $fillable = [
        'title',
        'description',
        'content',
        'link',
        'latitude',
        'longitude',
        'authors',
        'comments',
        'date'
    ];

    protected $dates = [
        'date'
    ];

    public function information() {
        return $this->hasOne(IncidentInformation::class);
    }
}