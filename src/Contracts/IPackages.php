<?php
namespace Jalno\Lumen\Contracts;

use Psr\Container\ContainerInterface;
use Laravel\Lumen\Application;


interface IPackages extends ContainerInterface {
	public function __construct(Application $app);
	public function register(string $package): void;
	public function setPrimary(string $package): void;

	/**
	 * @return IPackage[]
	 */
	public function all(): array;

	public function boot(): void;
}
