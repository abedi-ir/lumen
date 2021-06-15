<?php
namespace Jalno\Lumen\Packages;

use RuntimeException;
use Laravel\Lumen\Routing\Router;
use Jalno\Lumen\Contracts\{IPackage, IStorage};

abstract class PackageAbstract implements IPackage {

	protected string $namespace;

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
		return $this->basePath() . DIRECTORY_SEPARATOR . "Database";
	}

	public function getMigrationPath(): string
	{
		return $this->getDatabasePath() . DIRECTORY_SEPARATOR . "Migrations";
	}

	public function setupRouter(Router $router): void
	{
		include_once $this->basePath() . DIRECTORY_SEPARATOR . "routes" . DIRECTORY_SEPARATOR . "web.php";
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
        $composer = json_decode(file_get_contents($this->basePath() . DIRECTORY_SEPARATOR . 'composer.json'), true);

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
		return app(IStorage::class, [$this]);
	}

	public function getStorageConfig(): array
	{
		return app('config')->has("jalno.lumen.filesystems.disks") ?
				config("jalno.lumen.filesystems.disks") :
				[
					'local' => [
						'driver' => 'local',
						'root' => storage_path('app'),
					],
					'public' => [
						'driver' => 'local',
						'root' => storage_path('app/public'),
						'url' => env('APP_URL').'/storage',
						'visibility' => 'public',
					],
					's3' => [
						'driver' => 's3',
						'key' => env('AWS_ACCESS_KEY_ID'),
						'secret' => env('AWS_SECRET_ACCESS_KEY'),
						'region' => env('AWS_DEFAULT_REGION'),
						'bucket' => env('AWS_BUCKET'),
						'url' => env('AWS_URL'),
						'endpoint' => env('AWS_ENDPOINT'),
					],
				];
	}
}
