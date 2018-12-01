<?php

namespace Adewra\TrafficScotland\Console;

use Adewra\TrafficScotland\Http\RoadworksController;
use Illuminate\Console\Command;

class RoadworksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trafficscotland:roadworks
                            {
                                --current : Current Roadworks
                                --planned : Planned Roadworks
                            }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect the Current or Planned Roadworks from Traffic Scotland';

    /**
     * Execute the console command.
     *
     * @param RoadworksController $controller
     * @return mixed
     */
    public function handle(RoadworksController $controller)
    {
        $controller->index();
        return;
    }
}