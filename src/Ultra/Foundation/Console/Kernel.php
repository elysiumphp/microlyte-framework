<?php

declare(strict_types=1);

namespace Ultra\Foundation\Console;

use Closure;
use DateTimeInterface;
use Ultra\Console\Application as Extreme;
use Ultra\Console\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Throwable;

class Kernel
{

    /**
     * The application implementation.
     *
     * @var \Ultra\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The event dispatcher implementation.
     *
     * @var \Ultra\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The Symfony event dispatcher implementation.
     *
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|null
     */
    protected $symfonyDispatcher;

    /**
     * The Extreme application instance.
     *
     * @var \Ultra\Console\Application|null
     */
    protected $extreme;

    /**
     * The Extreme commands provided by the application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * The paths where Artisan commands should be automatically discovered.
     *
     * @var array
     */
    protected $commandRoutePaths = [];

    /**
     * Indicates if the Closure commands have been loaded.
     *
     * @var bool
     */
    protected $commandsLoaded = false;

    /**
     * The commands paths that have been "loaded".
     *
     * @var array
     */
    protected $loadedPaths = [];

    /**
     * All of the registered command duration handlers.
     *
     * @var array
     */
    protected $commandLifecycleDurationHandlers = [];

    /**
     * When the currently handled command started.
     *
     * @var \Illuminate\Support\Carbon|null
     */
    protected $commandStartedAt;

    /**
     * Create a new console kernel instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(Application $app, Dispatcher $events)
    {
        if (! defined('EXTREME_BINARY')) {
            define('EXTREME_BINARY', 'extreme');
        }

        $this->app = $app;
        $this->events = $events;

        $this->app->booted(function () {
            if (! $this->app->runningUnitTests()) {
                $this->rerouteSymfonyCommandEvents();
            }
        });
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Ultra\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Resolve a console schedule instance.
     *
     * @return \Ultra\Console\Scheduling\Schedule
     */
    public function resolveConsoleSchedule()
    {
        return tap(new Schedule($this->scheduleTimezone()), function ($schedule) {
            $this->schedule($schedule->useCache($this->scheduleCache()));
        });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        //
    }

    /**
     * Register a Closure based command with the application.
     *
     * @param  string  $signature
     * @param  \Closure  $callback
     * @return \Illuminate\Foundation\Console\ClosureCommand
     */
    public function command($signature, Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        Extreme::starting(function ($extreme) use ($command) {
            $extreme->add($command);
        });

        return $command;
    }

    /**
     * Register all of the commands in the given directory.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function load($paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $this->loadedPaths = array_values(
            array_unique(array_merge($this->loadedPaths, $paths))
        );

        $namespace = $this->app->getNamespace();

        foreach (Finder::create()->in($paths)->files() as $file) {
            $command = $this->commandClassFromFile($file, $namespace);

            if (is_subclass_of($command, Command::class) &&
                ! (new ReflectionClass($command))->isAbstract()) {
                Extreme::starting(function ($extreme) use ($command) {
                    $extreme->resolve($command);
                });
            }
        }
    }
}
