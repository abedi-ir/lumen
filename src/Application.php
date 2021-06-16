<?php
namespace Jalno\Lumen;

use Jalno\Lumen\Contracts\IPackages;
use Jalno\Storage\Providers\StorageProvider;
use Laravel\Lumen\Application as ParentApplication;
use Jalno\AutoDiscovery\Providers\AutoDiscoverProvider;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

class Application extends ParentApplication
{
    public IPackages $packages;

    /**
     * Create a new Lumen application instance.
     *
     * @param  string|null  $basePath
     * @param class-string<Contracts\IPackage> $primaryPackage
     * @param IPackages $packages
     * @return void
     */
    public function __construct($basePath, string $primaryPackage, IPackages $packages)
    {
        $this->packages = $packages;
        $this->register(Packages\PackagesServiceProvider::class);
        $this->packages->setPrimary($primaryPackage);
        parent::__construct($basePath);
        $this->singleton(ConsoleKernelContract::class, Console\Kernel::class);
        $this->register(AutoDiscoverProvider::class);
        $this->register(StorageProvider::class);
    }

    /**
     * Get the path to the application "src" directory.
     *
     * @return string
     */
    public function path()
    {
        $primaryPackage = $this->packages->getPrimary();
        if ($primaryPackage) {
            return $primaryPackage->basePath();
        }
        return parent::path();
    }

    /**
     * Get the path to the database directory.
     *
     * @param  string  $path
     * @return string
     */
    public function databasePath($path = '')
    {
        $primaryPackage = $this->packages->getPrimary();
        if ($primaryPackage) {
            return $primaryPackage->getDatabasePath().($path ? DIRECTORY_SEPARATOR.$path : $path);
        }
        return parent::databasePath($path);
    }

    /**
     * Prepare the application to execute a console command.
     *
     * @param  bool  $aliases
     * @return void
     */
    public function prepareForConsoleCommand($aliases = true)
    {
        parent::prepareForConsoleCommand($aliases);
        $this->register(Database\MigrationServiceProvider::class);
    }
}
