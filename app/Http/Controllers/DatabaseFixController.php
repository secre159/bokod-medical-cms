<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class DatabaseFixController extends Controller
{
    /**
     * Show database fix dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. Admin access required.');
        }
        
        $checks = $this->runChecks();
        return view('admin.database-fixes', compact('checks'));
    }

    /**
     * Run database checks
     */
    private function runChecks()
    {
        $checks = [
            'messaging' => $this->checkMessagingSystem(),
            'prescriptions' => $this->checkPrescriptionsSystem(),
        ];

        return $checks;
    }

    /**
     * Check messaging system
     */
    private function checkMessagingSystem()
    {
        $issues = [];
        $fixes = [];

        try {
            // Check conversations-patient relationships
            if (Schema::hasTable('conversations') && Schema::hasTable('patients')) {
                $conversations = DB::table('conversations')->get();
                $brokenRelationships = 0;

                foreach ($conversations as $conv) {
                    $patient = DB::table('patients')->where('id', $conv->patient_id)->first();
                    if (!$patient) {
                        $brokenRelationships++;
                        // Try to find correct patient
                        $messageSenders = DB::table('messages')
                            ->join('users', 'messages.sender_id', '=', 'users.id')
                            ->where('messages.conversation_id', $conv->id)
                            ->where('users.role', 'patient')
                            ->distinct()
                            ->select('users.id as user_id', 'users.name')
                            ->first();

                        if ($messageSenders) {
                            $correctPatient = DB::table('patients')
                                ->where('user_id', $messageSenders->user_id)
                                ->first();

                            if ($correctPatient) {
                                $fixes[] = [
                                    'conversation_id' => $conv->id,
                                    'old_patient_id' => $conv->patient_id,
                                    'new_patient_id' => $correctPatient->id,
                                    'patient_name' => $correctPatient->patient_name
                                ];
                            }
                        }
                    }
                }

                if ($brokenRelationships > 0) {
                    $issues[] = "Found {$brokenRelationships} conversations with broken patient relationships";
                }
            }
        } catch (\Exception $e) {
            $issues[] = "Error checking messaging system: " . $e->getMessage();
        }

        return [
            'status' => empty($issues) ? 'ok' : 'needs_fix',
            'issues' => $issues,
            'fixes' => $fixes
        ];
    }

    /**
     * Check prescriptions system
     */
    private function checkPrescriptionsSystem()
    {
        $issues = [];
        $missingColumns = [];

        try {
            if (!Schema::hasTable('prescriptions')) {
                $issues[] = "Prescriptions table does not exist";
                return ['status' => 'error', 'issues' => $issues, 'fixes' => []];
            }

            $requiredColumns = [
                'prescribed_by' => 'BIGINT UNSIGNED NULL',
                'frequency' => "VARCHAR(255) DEFAULT 'once_daily'",
                'duration_days' => 'INTEGER NULL',
                'dispensed_quantity' => 'INTEGER DEFAULT 0',
                'remaining_quantity' => 'INTEGER NULL'
            ];

            foreach (array_keys($requiredColumns) as $column) {
                if (!Schema::hasColumn('prescriptions', $column)) {
                    $missingColumns[] = $column;
                }
            }

            if (!empty($missingColumns)) {
                $issues[] = "Missing columns in prescriptions table: " . implode(', ', $missingColumns);
            }
        } catch (\Exception $e) {
            $issues[] = "Error checking prescriptions system: " . $e->getMessage();
        }

        return [
            'status' => empty($issues) ? 'ok' : 'needs_fix',
            'issues' => $issues,
            'fixes' => $missingColumns
        ];
    }

    /**
     * Fix messaging system issues
     */
    public function fixMessaging(Request $request)
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $fixCount = 0;
            $conversations = DB::table('conversations')->get();
            $patients = DB::table('patients')->get();

            foreach ($conversations as $conv) {
                $patient = $patients->where('id', $conv->patient_id)->first();

                if (!$patient) {
                    // Try to find the correct patient by looking at message senders
                    $messageSenders = DB::table('messages')
                        ->join('users', 'messages.sender_id', '=', 'users.id')
                        ->where('messages.conversation_id', $conv->id)
                        ->where('users.role', 'patient')
                        ->distinct()
                        ->select('users.id as user_id', 'users.name')
                        ->first();

                    if ($messageSenders) {
                        $correctPatient = $patients->where('user_id', $messageSenders->user_id)->first();

                        if ($correctPatient) {
                            DB::table('conversations')
                                ->where('id', $conv->id)
                                ->update(['patient_id' => $correctPatient->id]);
                            $fixCount++;
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Fixed {$fixCount} conversation relationships",
                'fixes_applied' => $fixCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error fixing messaging system: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fix prescriptions system issues
     */
    public function fixPrescriptions(Request $request)
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $requiredColumns = [
                'prescribed_by' => 'BIGINT UNSIGNED NULL',
                'frequency' => "VARCHAR(255) DEFAULT 'once_daily'",
                'duration_days' => 'INTEGER NULL',
                'dispensed_quantity' => 'INTEGER DEFAULT 0',
                'remaining_quantity' => 'INTEGER NULL'
            ];

            $addedColumns = [];

            foreach ($requiredColumns as $column => $definition) {
                if (!Schema::hasColumn('prescriptions', $column)) {
                    DB::statement("ALTER TABLE prescriptions ADD COLUMN {$column} {$definition}");
                    $addedColumns[] = $column;
                }
            }

            // Add foreign key constraint for prescribed_by if it was added
            if (in_array('prescribed_by', $addedColumns)) {
                // Check if constraint already exists first
                $constraintExists = DB::select("
                    SELECT constraint_name 
                    FROM information_schema.table_constraints 
                    WHERE table_name = 'prescriptions' 
                    AND constraint_name = 'prescriptions_prescribed_by_foreign'
                ");

                if (empty($constraintExists)) {
                    DB::statement("ALTER TABLE prescriptions ADD CONSTRAINT prescriptions_prescribed_by_foreign FOREIGN KEY (prescribed_by) REFERENCES users(id) ON DELETE SET NULL");
                }
            }

            // Update migrations table if exists
            if (Schema::hasTable('migrations')) {
                $migrationName = '2025_09_17_030723_add_comprehensive_fields_to_prescriptions_table';
                $migrationExists = DB::table('migrations')->where('migration', $migrationName)->exists();

                if (!$migrationExists) {
                    DB::table('migrations')->insert([
                        'migration' => $migrationName,
                        'batch' => DB::table('migrations')->max('batch') + 1
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Added " . count($addedColumns) . " missing columns to prescriptions table",
                'columns_added' => $addedColumns
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error fixing prescriptions system: ' . $e->getMessage()
            ], 500);
        }
    }
}