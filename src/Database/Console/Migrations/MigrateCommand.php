<?php
namespace Jalno\Lumen\Database\Console\Migrations;

use Illuminate\Database\Console\Migrations\MigrateCommand as ParentCommand;

class MigrateCommand extends ParentCommand
{
	use MigratePathTrait;

    /**
     * The Laravel application instance.
     *
     * @var \Jalno\Lumen\Application
     */
    protected $laravel;

	/**
     * Get all of the migration paths.
     *
     * @return array
     */
    protected function getMigrationPaths()
    {
        // Here, we will check to see if a path option has been defined. If it has we will
        // use the path relative to the root of the installation folder so our database
        // migrations may be run for any customized path from within the application.
        if ($this->input->hasOption('path') && $this->option('path')) {
            return collect($this->option('path'))->map(function ($path) {
                return ! $this->usingRealPath()
                                ? $this->laravel->basePath().'/'.$path
                                : $path;
            })->all();
        }
        $result = array_merge(
            $this->migrator->paths(), $this->dependenciesMigrationPath(), [$this->getMigrationPath()]
        );
        $migrationPath = $this->getMigrationPath();
        return $migrationPath ? array_merge($result, [$migrationPath]) : $result;
    }
}
