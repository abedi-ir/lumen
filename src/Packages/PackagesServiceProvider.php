<?php
namespace Jalno\Lumen\Packages;

use Jalno\Lumen\Contracts;
use Illuminate\Support\ServiceProvider;

class PackagesServiceProvider extends ServiceProvider {
	public function register()
    {
		$package = new Repository($this->app);
		$this->app->instance(Contracts\IPackages::class, $package);
		$this->app->instance("packages", $package);
    }
}
