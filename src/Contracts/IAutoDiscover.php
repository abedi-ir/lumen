<?php
namespace Jalno\Lumen\Contracts;

interface IAutoDiscover {
	public function __construct(IPackage $package);
	public function register(): void;
}