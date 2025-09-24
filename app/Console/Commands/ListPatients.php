<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;

class ListPatients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:patients {--duplicates : Only show duplicates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all patients in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $patients = Patient::orderBy('patient_name')->get(['id', 'patient_name', 'email', 'created_at']);
        
        if ($this->option('duplicates')) {
            // Show only patients with duplicate names
            $duplicates = $patients->groupBy('patient_name')
                                 ->filter(function($group) {
                                     return $group->count() > 1;
                                 });
            
            if ($duplicates->isEmpty()) {
                $this->info('No duplicate patients found.');
                return;
            }
            
            $this->info('Duplicate patients found:');
            $this->line('=======================');
            
            foreach ($duplicates as $name => $group) {
                $this->warn("\n👥 {$name} ({$group->count()} records):");
                foreach ($group as $patient) {
                    $this->line("   ID {$patient->id}: {$patient->email} (Created: {$patient->created_at->format('Y-m-d H:i')})");
                }
            }
        } else {
            // Show all patients
            $this->info('All patients in database:');
            $this->line('=========================');
            
            foreach ($patients as $patient) {
                $this->line("{$patient->id}: {$patient->patient_name} ({$patient->email})");
            }
            
            $this->info("\nTotal: {$patients->count()} patients");
            
            // Check for duplicates
            $duplicateCount = $patients->groupBy('patient_name')
                                     ->filter(function($group) {
                                         return $group->count() > 1;
                                     })->count();
            
            if ($duplicateCount > 0) {
                $this->warn("⚠️  Found {$duplicateCount} duplicate names. Run with --duplicates to see details.");
            }
        }
    }
}
