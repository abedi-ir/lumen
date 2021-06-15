<?php
namespace Jalno\Lumen\Database\Console\Migrations;

use Jalno\Lumen\Contracts\IPackage;

trait MigratePathTrait {
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
        return array_merge(
            $this->migrator->paths(), $this->dependenciesMigrationPath(), [$this->getMigrationPath()]
        );
    }

    protected function dependenciesMigrationPath(?IPackage $package = null): array
    {
        $packages = $this->laravel->packages;
        if ($package === null) {
            $package = $packages->getPrimary();
        }
        $paths = [];
        foreach ($package->getDependencies() as $dependencyName) {
            $dependency = $packages->get($dependencyName);
            array_push($paths, ...$this->dependenciesMigrationPath($dependency));
            $paths[] = $dependency->getMigrationPath();
        }
        return $paths;
    }

	/**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return $this->laravel->packages->getPrimary()->getMigrationPath();
    }
}