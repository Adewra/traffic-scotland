<?php

namespace Adewra\TrafficScotland\Console;

use Adewra\TrafficScotland\Http\CurrentIncidentsController;
use Illuminate\Console\Command;

class CurrentIncidentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trafficscotland:currentincidents
                            {--fictional-option : Some fictional option that does nothing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect the Current Incidents from Traffic Scotland';

    /**
     * Execute the console command.
     *
     * @param CurrentIncidentsController
     * @return mixed
     */
    public function handle(CurrentIncidentsController $controller)
    {
        $controller->index();
        return;
    }
}