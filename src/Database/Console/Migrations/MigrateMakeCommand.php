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
        /** @var string|null $targetPath */
        $targetPath = $this->input->getOption('path');
        if (! is_null($targetPath)) {
            return ! $this->usingRealPath()
                            ? $this->laravel->basePath() . '/' . $targetPath
                            : $targetPath;
        }

        return $this->jalnoGetMigrationPath();
    }
}
