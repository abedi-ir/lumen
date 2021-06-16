<?php
namespace Jalno\Lumen\Database\Console\Migrations;

use Jalno\Lumen\Contracts\IPackage;

trait MigratePathTrait {

    /**
     * @param IPackage|null $package
     * @return string[]
     */
    protected function dependenciesMigrationPath(?IPackage $package = null): array
    {
        /** @var \Jalno\Lumen\Application */
        $laravel = $this->laravel;
        $packages = $laravel->packages;
        if ($package === null) {
            $package = $packages->getPrimary();
        }
        $paths = [];
        if (!$package) {
            return $paths;
        }
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
     * @return string|null
     */
    protected function getMigrationPath()
    {
        $primaryPackage = $this->laravel->packages->getPrimary();
        return $primaryPackage ? $primaryPackage->getMigrationPath() : null;
    }
}