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
                                { --current : Current Roadworks }
                                { --planned : Planned Roadworks }
                            ';

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
     * @throws
     */
    public function handle(RoadworksController $controller)
    {
        if($this->option('current') == null && $this->option('planned') == null)
            $currentPlannedOrBoth = $this->anticipate('Would you like current, planned or both?', ['current','planned','both']);

        $current = $this->option('current') ?? false;
        $planned = $this->option('planned') ?? false;

        if(isset($currentPlannedOrBoth))
        {
            switch (strtolower($currentPlannedOrBoth)) {
                case "current":
                    $current = true;
                    break;
                case "planned":
                    $planned = true;
                    break;
                case "both":
                    $current = true;
                    $planned = true;
                    break;
            }
        }

        $controller->index($current, $planned);
        return;
    }
}