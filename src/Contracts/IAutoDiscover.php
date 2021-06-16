<?php
namespace Jalno\Lumen\Contracts;

use Laravel\Lumen\Application;

interface IAutoDiscover {
	public function __construct(Application $app);
	public function register(): void;
}