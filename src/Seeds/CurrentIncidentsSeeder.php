<?php

namespace Adewra\TrafficScotland;

use Illuminate\Database\Seeder;

class CurrentIncidentsSeeder extends Seeder
{
    public function run()
    {
        \DB::table('current_incidents')->insert([
            'title' => 'M8 J19 Clydeside Expressway - Queue',
            'description' => '3 lanes restricted Eastbound indefinitely',
            'link' => 'http://tscot.org/01c223189',
            'latitude' => '56.86653398087',
            'longitude' => '-4.27017818778178',
            'date' => '2018-11-25',
            'extended_details' => '{"date":"2018-11-25","start_time":"15:55:40","location":"M8 J19 Clydeside Expressway","direction":"Eastbound","type":"Queue","description":"3 lanes restricted Eastbound indefinitely"}',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('current_incidents')->insert([
            'title' => 'A82 Spean Bridge - A830 - Accident',
            'description' => 'The A82 at Fort William Golf Club is currently restricted due to a road traffic accident.  Road users are advised to use caution on approach and allow extra time for their journey.',
            'link' => 'http://tscot.org/01c223198',
            'latitude' => '55.8593175829683',
            'longitude' => '-5.0046034298707',
            'date' => '2018-11-25',
            'extended_details' => '{"date":"2018-11-25","start_time":"17:25:55","location":"A82 Spean Bridge - A830","direction":"Northbound & Southbound","type":"Accident","description":"The A82 at Fort William Golf Club is currently restricted due to a road traffic accident. Road users are advised to use caution on approach and allow extra time for their journey."}',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('current_incidents')->insert([
            'title' => 'A9000 Forth Road Bridge - High winds',
            'description' => 'Road users are advised to use caution crossing the A90 Forth Road Bridge due to high winds currently affecting driving conditions.',
            'link' => 'http://tscot.org/01c223503',
            'latitude' => '55.999703069431',
            'longitude' => '-3.4041775536689',
            'date' => '2018-11-29',
            'weather_conditions' => '{"start_time":"29 Nov 2018 - 17:29","location":"A9000 Forth Road Bridge","type":"High winds","route_name":"A9000","direction":"Northbound & Southbound","description":"Road users are advised to use caution crossing the A90 Forth Road Bridge due to high winds currently affecting driving conditions.","expected_duration":"Unknown"}',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('current_incidents')->insert([
            'title' => 'Grampian - Low temperature',
            'description' => 'Road users are advised to drive with care due to low temperatures affecting driving conditions on many roads throughout the region.',
            'link' => 'http://tscot.org/01a9706',
            'latitude' => '57.163647',
            'longitude' => '-2.321385',
            'date' => '2018-11-30',
            'weather_conditions' => '{"title":"Low Temp - Grampian","start_time":"30 Nov 2018 - 21:11","location":"Grampian","type":"Low temperature","route_name":"All Routes","direction":"Any Direction","description":"Road users are advised to drive with care due to low temperatures affecting driving conditions on many roads throughout the region."}',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        /* Below are two sample items from Highways England incidents with extended details in the description field for testing support later */
        /* Ideally with the information in extended_details rather than  in description. */
        /*
         <item>
          <title>A1 - Accident</title>
          <description>TYPE : GDP Location : The A1 northbound between the junctions with the A1068  and the A1167 south of Berwick-Upon-Tweed Reason : Road traffic collision Status : Currently Active Time To Clear : The event is expected to clear between 20:00 and 20:15 on 25 November 2018 Return To Normal : Normal traffic conditions are expected between 20:00 and 20:15 on 25 November 2018 Lanes Closed : All lanes are closed </description>
          <link>http://tscot.org/01h1125700427</link>
          <georss:point>55.5843845426895 -1.80805498780607</georss:point>
          <author />
          <comments />
          <pubDate>Sun, 25 Nov 2018 17:59:40 GMT</pubDate>
        </item>
        *
        <item>
          <title>A1 - RoadOrCarriagewayOrLaneManagement</title>
          <description>TYPE : GDP Location : The A1 southbound between the junctions with the A1167 south of Berwick-Upon-Tweed and the A1068  Reason : Road Management Status : Currently Active Time To Clear : The event is expected to clear between 20:00 and 20:15 on 25 November 2018 Return To Normal : Normal traffic conditions are expected between 20:00 and 20:15 on 25 November 2018 Lanes Closed : All lanes are closed </description>
          <link>http://tscot.org/01h1125700431</link>
          <georss:point>55.6054464212679 -1.82015797084324</georss:point>
          <author />
          <comments />
          <pubDate>Sun, 25 Nov 2018 18:02:57 GMT</pubDate>
        </item>
         *
         */
    }
}
