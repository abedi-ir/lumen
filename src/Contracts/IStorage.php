<?php
namespace Jalno\Lumen\Contracts;

use Illuminate\Filesystem\FilesystemAdapter;

interface IStorage {

	public function __construct(IPackage $package);

	public function public(): FilesystemAdapter;
	public function private(): FilesystemAdapter;
	public function disk(string $name): FilesystemAdapter;
}