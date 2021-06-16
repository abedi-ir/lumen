<?php

namespace Jalno\Lumen\Database;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigrator();
        $this->registerCreator();
        $this->registerCommands();
    }

    /**
     * Register the migrator service.
     *
     * @return void
     */
    protected function registerMigrator()
    {
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
        $this->app->singleton('migrator', function ($app) {
            $repository = $app['migration.repository'];

            return new Migrations\Migrator($repository, $app['db'], $app['files'], $app['events'], $app['packages']);
        });
    }

    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new Migrations\MigrationCreator($app['files'], $app->basePath('stubs'), $app['packages']->getPrimary());
        });
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->registerMigrateCommand();
        $this->registerMigrateMakeCommand();
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.migrate', function ($app) {
            return new Console\Migrations\MigrateCommand($app['migrator'], $app[Dispatcher::class]);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateMakeCommand()
    {
        $this->app->singleton('command.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            return new Console\Migrations\MigrateMakeCommand($creator, $composer);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'migration.creator',
            'command.migrate',
            'command.migrate.make',
        ];
    }
}
