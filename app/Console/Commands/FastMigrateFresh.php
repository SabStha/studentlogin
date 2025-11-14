<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FastMigrateFresh extends Command
{
    protected $signature = 'migrate:fast-fresh {--seed : Indicates if the seed task should be re-run}';
    protected $description = 'Drop all tables and re-run all migrations (faster version)';

    public function handle()
    {
        $this->info('Dropping all tables...');

        // Disable foreign key checks for faster dropping
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Get all table names
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = 'Tables_in_' . $databaseName;

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            if ($tableName !== 'migrations') {
                Schema::dropIfExists($tableName);
                $this->line("Dropped: {$tableName}");
            }
        }

        // Re-enable foreign key checks
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info('All tables dropped successfully.');
        $this->info('Running migrations...');

        // Run migrations
        $this->call('migrate', ['--force' => true]);

        // Run seeders if requested
        if ($this->option('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }

        $this->info('Done!');
    }
}

