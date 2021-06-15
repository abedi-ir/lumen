<?php
namespace Jalno\Lumen\Database\Migrations;

use Illuminate\Database\Migrations\MigrationCreator as ParentMigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Jalno\Lumen\Contracts\IPackage;

class MigrationCreator extends ParentMigrationCreator
{
    protected IPackage $package;
    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct(Filesystem $files, $customStubPath, IPackage $package)
    {
        parent::__construct($files, $customStubPath);
        $this->package = $package;
    }
    
    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table, ?string $namespace = null)
    {
        $stub = parent::populateStub($name, $stub, $table);
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }
        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        return $stub;
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return "M_" . $this->getDatePrefix() . "_" . Str::studly($name);
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('YmdHis');
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path . DIRECTORY_SEPARATOR . $this->getClassName($name) . '.php';
    }

    public function getNamespace(): string 
    {
        return $this->package->getNamespace() . "\\Database\\Migrations";
    }
}
