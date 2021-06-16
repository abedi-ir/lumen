<?php
namespace Jalno\Lumen;

use Jalno\Lumen\Contracts\IPackages;
use Jalno\Storage\Providers\StorageProvider;
use Laravel\Lumen\Application as ParentApplication;
use Jalno\AutoDiscovery\Providers\AutoDiscoverProvider;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

class Application extends ParentApplication
{
    protected $packages;
    protected $packagePath;
    /**
     * Create a new Lumen application instance.
     *
     * @param  string|null  $basePath
     * @param class-string<Contracts\IPackage>
     * @return void
     */
    public function __construct($basePath = null, string $primaryPackage, IPackages $packages)
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
        return $this->packages->getPrimary()->basePath();
    }

    /**
     * Get the path to the database directory.
     *
     * @param  string  $path
     * @return string
     */
    public function databasePath($path = '')
    {
        return $this->packages->getPrimary()->getDatabasePath().($path ? DIRECTORY_SEPARATOR.$path : $path);
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
