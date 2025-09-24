<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class CleanupPatients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:patients {--dry-run : Show what will be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate patients, keeping the one with the most medical data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $patients = Patient::orderBy('patient_name')->get();
        $duplicates = $patients->groupBy('patient_name')
                              ->filter(function($group) {
                                  return $group->count() > 1;
                              });
        
        if ($duplicates->isEmpty()) {
            $this->info('No duplicate patients found.');
            return;
        }
        
        $this->info('Found ' . $duplicates->count() . ' groups of duplicate patients.');
        
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made\n');
        }
        
        foreach ($duplicates as $name => $group) {
            $this->line("\n👥 Processing: {$name} ({$group->count()} records)");
            
            // Calculate which patient has the most data
            $patientScores = [];
            
            foreach ($group as $patient) {
                $score = 0;
                $score += $patient->appointments()->count() * 2;
                $score += $patient->visits()->count() * 3;
                $score += $patient->prescriptions()->count() * 2;
                $score += $patient->medicalNotes()->count() * 1;
                
                // Prefer older records (created first)
                if ($patient->created_at) {
                    $score += (time() - $patient->created_at->timestamp) / (60 * 60 * 24); // Add days since creation
                }
                
                $patientScores[$patient->id] = [
                    'patient' => $patient,
                    'score' => $score,
                    'appointments' => $patient->appointments()->count(),
                    'visits' => $patient->visits()->count(),
                    'prescriptions' => $patient->prescriptions()->count(),
                    'notes' => $patient->medicalNotes()->count()
                ];
            }
            
            // Sort by score (highest first)
            uasort($patientScores, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            $keepPatient = array_values($patientScores)[0];
            $deletePatients = array_slice($patientScores, 1);
            
            $this->line("   ✅ KEEP: ID {$keepPatient['patient']->id} ({$keepPatient['patient']->email})");
            $this->line("      Data: {$keepPatient['appointments']} appointments, {$keepPatient['visits']} visits, {$keepPatient['prescriptions']} prescriptions, {$keepPatient['notes']} notes");
            
            foreach ($deletePatients as $deleteData) {
                $deletePatient = $deleteData['patient'];
                $this->line("   ❌ DELETE: ID {$deletePatient->id} ({$deletePatient->email})");
                $this->line("      Data: {$deleteData['appointments']} appointments, {$deleteData['visits']} visits, {$deleteData['prescriptions']} prescriptions, {$deleteData['notes']} notes");
                
                if (!$this->option('dry-run')) {
                    try {
                        // Delete the patient (cascade will handle related records)
                        $deletePatient->delete();
                        $this->line("      ✓ Deleted successfully");
                    } catch (\Exception $e) {
                        $this->error("      ✗ Error deleting: " . $e->getMessage());
                    }
                }
            }
        }
        
        if ($this->option('dry-run')) {
            $this->info('\n🔍 Dry run complete. Use without --dry-run to actually delete duplicates.');
        } else {
            $this->info('\n✅ Cleanup complete!');
        }
        
        // Show final count
        $finalCount = Patient::count();
        $this->info("Final patient count: {$finalCount}");
    }
}
