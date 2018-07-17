<?php

use Illuminate\Support\Facades\App;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Prevent seeding in production
        if (App::environment() != 'production')
        {
            // Enable mass assignment while seeding
            Eloquent::unguarded(function ()
            {
                $this->truncateDatabase();

                $this->call(RolesAndAbilitiesSeeder::class);
                $this->call(JobsSeeder::class);
                $this->call(MembersSeeder::class);
                $this->call(SelectionProcessSeeder::class);
                $this->call(StudentSeeder::class);
            });
        }
    }

    /**
     * Truncate all tables (delete all rows), except migrations.
     */
    protected function truncateDatabase() {
        $connection = env('DB_CONNECTION', config('database.default'));
        $driver = config("database.connections.$connection.driver");

        // SQLite implementation only...
        if ($driver == 'sqlite') {

            // Forget about foreign keys
            DB::connection()->getPdo()->exec("pragma foreign_keys=0");

            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");

            // Get the 'name' property of returned stdObjects
            $tables = array_map(function ($table) {
                return $table->name;
            }, $tables);

            // Filter migrations and sqlite tables
            $tables = array_diff($tables, [config('database.migrations'), 'sqlite_sequence']);

            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }

            // Bring foreign keys back to normal
            DB::connection()->getPdo()->exec("pragma foreign_keys=1");
        }
    }
}
