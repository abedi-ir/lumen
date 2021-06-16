<?php
namespace Jalno\Lumen\Database\Migrations;

use Exception;
use ReflectionClass;
use Illuminate\Database\Migrations\{Migrator as ParentMigrator, MigrationRepositoryInterface};
use Illuminate\Support\{Str, Collection};
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Jalno\Lumen\Contracts\IPackages;

class Migrator extends ParentMigrator
{
    protected IPackages $packages;

    /**
     * Create a new migrator instance.
     *
     * @param MigrationRepositoryInterface  $repository
     * @param Resolver  $resolver
     * @param Filesystem  $files
     * @param Dispatcher|null  $dispatcher
     * @param IPackages $packages
     * @return void
     */
    public function __construct(MigrationRepositoryInterface $repository,
                                Resolver $resolver,
                                Filesystem $files,
                                Dispatcher $dispatcher = null,
                                IPackages $packages)
    {
        parent::__construct($repository, $resolver, $files, $dispatcher);
        $this->packages = $packages;
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function resolve($file)
    {
        return new $file;
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string|string[] $paths
     * @return string[]
     */
    public function getMigrationFiles($paths)
    {
        $files = Collection::make($paths)
            ->flatMap(function ($path) {
                return Str::endsWith($path, '.php') ? [$path] : $this->files->glob($path.'/*_*.php');
            })
            ->filter()
            ->values()
            ->all();

        return Collection::make($this->sortMigratationBasedOnPackages($files))
            ->keyBy(function ($file) {
                return $this->getMigrationName($file);
            })->all();
    }

    /**
     * @param string[] $files
     * @return string[] $files
     */
    public function sortMigratationBasedOnPackages(array $files): array
    {
        $packages = array_values($this->packages->all());
        $paths = array_values(array_map(fn($package) => $package->basePath(), $packages));
        $findPositionOfPackage = function($file) use ($paths): int
        {
            foreach ($paths as $x => $path) {
                if (substr($file, 0, strlen($path) + 1) == $path . DIRECTORY_SEPARATOR) {
                    return $x;
                }
            }
            return -1;
        };
        usort($files, function(string $a, string $b) use (&$findPositionOfPackage): int 
        {
            $aPosition = $findPositionOfPackage($a);
            $bPosition = $findPositionOfPackage($b);
            if ($aPosition != $bPosition) {
                return $aPosition - $bPosition;
            }
            return basename($a) <=> basename($b);
        });
        return $files;
    }

    /**
     * Get the name of the migration.
     *
     * @param  string  $path
     * @return string
     */
    public function getMigrationName($path): string
    {
        /**
         * $path: /vendor/laravel/passport/src/../database/migrations/2016_06_01_000001_create_oauth_auth_codes_table.php
         * 
         * $file: /vendor/laravel/passport/database/migrations/2016_06_01_000001_create_oauth_auth_codes_table.php
         */
        /** @var string $path */
        $path = realpath($path);
        $this->files->requireOnce($path);
        foreach (get_declared_classes() as $class) {
            $reflection = new ReflectionClass($class);
            if ($reflection->getFileName() === $path) {
                return $class;
            }
        }
        throw new Exception("cannot find any class in this file");
    }

    /**
     * Resolve a migration instance from a migration path.
     *
     * @param  string  $path
     * @return object
     */
    protected function resolvePath(string $path)
    {
        $class = $this->getMigrationClass($this->getMigrationName($path));

        if (class_exists($class) && realpath($path) == (new ReflectionClass($class))->getFileName()) {
            return new $class;
        }

        $migration = $this->files->requireOnce($path);

        return is_object($migration) ? $migration : new $class;
    }

    /**
     * Generate a migration class name based on the migration file name.
     *
     * @param  string  $migrationName
     * @return string
     */
    protected function getMigrationClass(string $migrationName): string
    {
        return $migrationName;
    }
}
