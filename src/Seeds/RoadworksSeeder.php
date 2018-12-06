<?php

namespace Adewra\TrafficScotland;

use Illuminate\Database\Seeder;

class RoadworksSeeder extends Seeder
{
    public function run()
    {
        \DB::table('roadworks')->insert([
            'title' => 'A85 North of Glen Ogle Farm',
            'description' => "Start Date: Sunday, 17 March 2019 - 00:00<br>End Date: Friday, 29 March 2019 - 00:00<br>Works:
Resurfacing

Traffic Management:
Convoy Working (10mph)",
            'link' => 'http://tscot.org/04pNW20183929',
            'latitude' => '56.399998335061',
            'longitude' => '-4.2996971475434',
            'date' => '2019-03-17',
            'start_date' => '2019-03-17 00:00:00',
            'end_date' => '2019-03-29 00:00:00',
            'works' => 'Resurfacing',
            'delay_information' => null,
            'traffic_management' => 'Convoy Working (10mph)',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('roadworks')->insert([
            'title' => 'A9 Prior to Dunning Jnct NB - Lane Closure',
            'description' => "Start Date: Monday, 21 January 2019 - 00:00<br>End Date: Friday, 25 January 2019 - 00:00<br>Works:
Utility Works

Traffic Management:
Lane Closure.",
            'link' => 'http://tscot.org/04pNE20184979',
            'latitude' => '56.334419862678',
            'longitude' => '-3.623350237648',
            'date' => '2019-01-21',
            'start_date' => '2019-01-21 00:00:00',
            'end_date' => '2019-01-25 00:00:00',
            'works' => 'Utility Works',
            'delay_information' => null,
            'traffic_management' => 'Lane Closure.',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('roadworks')->insert([
            'title' => 'A78 Auchengate',
            'description' => "Start Date: Wednesday, 19 December 2018 - 00:00<br>End Date: Wednesday, 19 December 2018 - 00:00<br>Works:
Parapet Repairs

Traffic Management:
Lane Closure.",
            'link' => 'http://tscot.org/04pSW20187075',
            'latitude' => '55.566764607922',
            'longitude' => '-4.6361839487985',
            'date' => '2018-12-19',
            'start_date' => '2018-12-19 00:00:00',
            'end_date' => '2018-12-19 00:00:00',
            'works' => 'Parapet Repairs',
            'delay_information' => null,
            'traffic_management' => 'Lane Closure.',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}
