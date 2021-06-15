<?php
namespace Jalno\Lumen\Contracts;


use Laravel\Lumen\Routing\Router;

interface IAutoDiscover {

	public function __construct(IPackage $package);
	public function register(): void;
}