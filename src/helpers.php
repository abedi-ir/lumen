<?php

use Jalno\Lumen\Contracts\{IPackage, IPackages};
use Illuminate\Container\EntryNotFoundException;

function packages(): IPackages {
	return app(IPackages::class);
}

function package(?string $name = null): IPackage {
	if ($name != null) {
		return packages()->get($name);
	}
	$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	$namespace = null;
	if (isset($caller['class'])) {
		$namespace = $caller['class'];
	} elseif (isset($caller['function'])) {
		$namespace = $caller['function'];
	}

	if (!$namespace) {
		throw new EntryNotFoundException();
	}

	$packages = packages()->all();
	foreach ($packages as $package) {
		$packageNamespace = $package->getNamespace();
		if (substr($namespace, 0, strlen($packageNamespace) + 1) == $packageNamespace . "\\") {
			return $package;
		}
	}
	throw new EntryNotFoundException();
}
