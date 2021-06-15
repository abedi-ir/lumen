<?php
namespace Jalno\Lumen\Database\Console\Migrations;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as ParentCommand;

class MigrateMakeCommand extends ParentCommand
{
    use MigratePathTrait {
        getMigrationPath as protected jalnoGetMigrationPath;
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                            ? $this->laravel->basePath().'/'.$targetPath
                            : $targetPath;
        }

        return $this->jalnoGetMigrationPath();
    }
}
