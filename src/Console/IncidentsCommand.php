<?php

namespace Adewra\TrafficScotland\Console;

use Adewra\TrafficScotland\Http\IncidentsController;
use Illuminate\Console\Command;

class IncidentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trafficscotland:incidents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect the current Incidents from Traffic Scotland';

    /**
     * Execute the console command.
     *
     * @param IncidentsController
     * @return mixed
     */
    public function handle(IncidentsController $controller)
    {
        $controller->index();
        return;
    }
}