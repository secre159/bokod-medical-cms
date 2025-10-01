<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class FixPostgreSQLConstraint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-constraint {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix PostgreSQL appointments status constraint to include overdue status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing PostgreSQL appointments status constraint...');
        
        if (!$this->option('force') && !$this->confirm('This will modify the database constraint. Continue?')) {
            $this->info('Operation cancelled.');
            return 1;
        }
        
        try {
            // Drop the existing constraint
            $this->info('Dropping existing constraint...');
            DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check');
            
            // Add the new constraint with overdue status
            $this->info('Adding new constraint with overdue status...');
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('active', 'cancelled', 'completed', 'overdue'))");
            
            $this->info('✅ PostgreSQL constraint fixed successfully!');
            $this->info('Appointments can now be set to overdue status.');
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ Failed to fix constraint: ' . $e->getMessage());
            return 1;
        }
    }
}
