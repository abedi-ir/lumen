<?php
namespace Jalno\Lumen\Database\Console\Migrations;

use Jalno\Lumen\Contracts\IPackage;

trait MigratePathTrait {

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