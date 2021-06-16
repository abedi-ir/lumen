<?php

namespace Jalno\Lumen\Console;

use Illuminate\Console\Scheduling\{Schedule, ScheduleRunCommand};
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Laravel\Lumen\Application;
use Jalno\Lumen\Contracts\IPackages;

class Kernel extends ConsoleKernel
{

    protected IPackages $packages;

    /**
     * Create a new console kernel instance.
     *
     * @param  \Laravel\Lumen\Application  $app
     * @return void
     */
    public function __construct(Application $app, IPackages $packages)
    {
        $this->packages = $packages;
        parent::__construct($app);
    }

    /**
     * Get the commands to add to the application.
     *
     * @return string[]
     */
    protected function getCommands()
    {
        $commands = [ScheduleRunCommand::class];
        foreach ($this->packages->all() as $package) {
            if (empty($package->getCommands())) {
                continue;
            }
            array_push($commands, ...$package->getCommands());
        }
        return $commands;
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
