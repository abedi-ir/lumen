<?php
namespace Jalno\Lumen\Packages;

use RuntimeException;
use Laravel\Lumen\Routing\Router;
use \Illuminate\Filesystem\FilesystemAdapter;
use Jalno\Lumen\Contracts\{IPackage, IStorage};

abstract class PackageAbstract implements IPackage {

    protected ?string $name = null;
    protected string $namespace;
    protected ?IStorage $storage = null;

    public function getName(): string
    {
        if (is_null($this->name)) {
            if (!is_file($this->path("..", "composer.json"))) {
                return $this->name = "";
            }
            $composer = json_decode(file_get_contents($this->path("..", "composer.json")), true);
            $this->name = $composer["name"] ?? "";
        }

        return $this->name;
    }

    public function getProviders(): array
    {
        return [];
    }

    public function getDependencies(): array
    {
        return [];
    }

    public function getDatabasePath(): string
    {
        return $this->path("Database");
    }

    public function getMigrationPath(): string
    {
        return $this->getDatabasePath() . DIRECTORY_SEPARATOR . "Migrations";
    }

    public function setupRouter(Router $router): void
    {
        if (!is_file($this->path("routes", "web.php"))) {
            return;
        }

        include_once $this->path("routes", "web.php");
    }

    /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace(): string
    {
        if (!isset($this->namespace)) {
            return $this->namespace;
        }
        $packageName = get_class($this);
        $composer = json_decode(file_get_contents($this->path("..", 'composer.json')), true);

        if (isset($composer['autoload']['psr-4'])) {
            foreach ($composer['autoload']['psr-4'] as $namespace => $path) {
                if (substr($packageName, 0, strlen($namespace)) == $namespace) {
                    return $this->namespace = rtrim($namespace, "\\");
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }

    public function path(string ...$paths): string {
        return $this->basePath() . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths);
    }

    /**
     * Get the commands to add to the application.
     *
     * @return array
     */
    public function getCommands(): array
    {
        return [];
    }

    public function storage(): IStorage
    {
        if (!$this->storage) {
            $this->storage = app(IStorage::class, [$this]);
        }

        return $this->storage;
    }

    public function disk(string $name): FilesystemAdapter
    {
        return $this->storage()->disk($name);
    }

    public function getStorageConfig(): array
    {
        $name = str_replace("/", ".", $this->getName());
        return app('config')->has("{$name}.filesystems.disks") ?
                config("{$name}.filesystems.disks") :
                [
                    'public' => [
                        'driver' => 'local',
                        'root' => storage_path('public/' . $this->getName()),
                        'url' => env('APP_URL') . '/storage/' . $this->getName(),
                        'visibility' => 'public',
                    ],
                    'private' => [
                        'driver' => 'local',
                        'root' => storage_path('private/' . $this->getName()),
                        'visibility' => 'private',
                    ],
                ];
    }
}
