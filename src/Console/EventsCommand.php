<?php

namespace Adewra\TrafficScotland\Console;

use Adewra\TrafficScotland\Http\EventsController;
use Illuminate\Console\Command;

class EventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trafficscotland:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect the Planned Events from Traffic Scotland';

    /**
     * Execute the console command.
     *
     * @param EventsController $controller
     * @return mixed
     * @throws
     */
    public function handle(EventsController $controller)
    {
        $controller->index();
        return;
    }
}