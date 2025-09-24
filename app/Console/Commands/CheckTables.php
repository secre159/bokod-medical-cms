<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if required tables exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = [
            'patients',
            'appointments', 
            'patient_visits',
            'patient_medication_history',
            'prescriptions',
            'medical_notes'
        ];
        
        $this->info('Checking database tables:');
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->line("✅ {$table} - exists ({$count} records)");
            } else {
                $this->error("❌ {$table} - does not exist");
            }
        }
        
        $this->info('\nTable check complete!');
    }
}
