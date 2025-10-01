<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPostgreSQLConstraintV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postgresql:fix-constraint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix PostgreSQL constraint to include overdue status for appointments table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting PostgreSQL constraint fix...');
        
        // Check if we're using PostgreSQL
        if (DB::getDriverName() !== 'pgsql') {
            $this->warn('Not using PostgreSQL, skipping constraint fix');
            return;
        }
        
        $this->info('Database driver: ' . DB::getDriverName());
        
        try {
            // Show current status values
            $statuses = DB::table('appointments')->distinct()->pluck('status');
            $this->info('Current appointment status values: ' . $statuses->implode(', '));
            
            // Drop existing constraint
            $this->info('Dropping existing constraint...');
            DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
            $this->info('✓ Existing constraint dropped');
            
            // Add new constraint with overdue status
            $this->info('Adding new constraint with overdue status...');
            DB::statement(
                "ALTER TABLE appointments ADD CONSTRAINT appointments_status_check 
                 CHECK (status IN ('pending', 'active', 'completed', 'cancelled', 'overdue'))"
            );
            $this->info('✓ New constraint added successfully!');
            
            $this->info('✓ PostgreSQL constraint fix completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error fixing constraint: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
