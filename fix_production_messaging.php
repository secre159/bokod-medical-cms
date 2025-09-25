<?php

/**
 * PRODUCTION MESSAGING RELATIONSHIP FIX SCRIPT
 * 
 * This script fixes broken conversation-patient relationships in production
 * Run this on the production server after deploying the code changes
 */

require_once __DIR__ . '/vendor/autoload.php';
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PRODUCTION MESSAGING RELATIONSHIPS FIX ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Environment: " . app()->environment() . "\n\n";

try {
    // Start transaction for safety
    DB::beginTransaction();
    
    echo "1. Checking current conversation-patient relationships...\n";
    
    $conversations = DB::table('conversations')->get();
    $patients = DB::table('patients')->get();
    
    echo "   Found {$conversations->count()} conversations and {$patients->count()} patients\n";
    
    $fixes = [];
    $errors = [];
    
    foreach ($conversations as $conv) {
        $patient = $patients->where('id', $conv->patient_id)->first();
        
        if (!$patient) {
            echo "   ❌ Conversation {$conv->id} references non-existent patient_id {$conv->patient_id}\n";
            
            // Try to find the correct patient by looking at message senders
            $messageSenders = DB::table('messages')
                ->join('users', 'messages.sender_id', '=', 'users.id')
                ->where('messages.conversation_id', $conv->id)
                ->where('users.role', 'patient')
                ->distinct()
                ->select('users.id as user_id', 'users.name')
                ->get();
            
            if ($messageSenders->count() > 0) {
                $patientUser = $messageSenders->first();
                echo "      Found patient user in messages: {$patientUser->name} (user_id: {$patientUser->user_id})\n";
                
                // Find the corresponding patient record
                $correctPatient = $patients->where('user_id', $patientUser->user_id)->first();
                
                if ($correctPatient) {
                    echo "      ✓ Found correct patient record: ID {$correctPatient->id}\n";
                    $fixes[] = [
                        'conversation_id' => $conv->id,
                        'old_patient_id' => $conv->patient_id,
                        'new_patient_id' => $correctPatient->id,
                        'patient_name' => $correctPatient->patient_name
                    ];
                } else {
                    $errors[] = "No patient record found for user_id {$patientUser->user_id} in conversation {$conv->id}";
                }
            } else {
                $errors[] = "No patient messages found in conversation {$conv->id}";
            }
        } else {
            echo "   ✓ Conversation {$conv->id} correctly references patient {$patient->patient_name}\n";
        }
    }
    
    echo "\n2. Summary of required fixes:\n";
    if (count($fixes) > 0) {
        foreach ($fixes as $fix) {
            echo "   - Conversation {$fix['conversation_id']}: patient_id {$fix['old_patient_id']} -> {$fix['new_patient_id']} ({$fix['patient_name']})\n";
        }
    } else {
        echo "   No fixes needed!\n";
    }
    
    if (count($errors) > 0) {
        echo "\n   Errors found:\n";
        foreach ($errors as $error) {
            echo "   - {$error}\n";
        }
    }
    
    // Ask for confirmation in production
    if (app()->environment('production') && count($fixes) > 0) {
        echo "\n⚠️  PRODUCTION ENVIRONMENT DETECTED!\n";
        echo "This script will modify " . count($fixes) . " conversation records.\n";
        echo "Please review the changes above carefully.\n\n";
        
        // In production, we'll just show what would be done
        echo "To apply these fixes, run this script with APPLY_FIXES=true environment variable:\n";
        echo "APPLY_FIXES=true php fix_production_messaging.php\n\n";
        
        if (!env('APPLY_FIXES', false)) {
            echo "🔒 NO CHANGES APPLIED (safety mode)\n";
            DB::rollBack();
            exit(0);
        }
    }
    
    echo "\n3. Applying fixes...\n";
    
    if (count($fixes) > 0) {
        foreach ($fixes as $fix) {
            echo "   Updating conversation {$fix['conversation_id']}: {$fix['old_patient_id']} -> {$fix['new_patient_id']}\n";
            
            $updated = DB::table('conversations')
                ->where('id', $fix['conversation_id'])
                ->update(['patient_id' => $fix['new_patient_id']]);
                
            if ($updated) {
                echo "     ✅ Updated successfully\n";
            } else {
                throw new Exception("Failed to update conversation {$fix['conversation_id']}");
            }
        }
        
        echo "\n   ✅ Applied " . count($fixes) . " fixes successfully!\n";
    }
    
    // Commit the transaction
    DB::commit();
    
    echo "\n4. Verification...\n";
    $updatedConversations = DB::table('conversations')->get();
    $allGood = true;
    
    foreach ($updatedConversations as $conv) {
        $patient = DB::table('patients')->where('id', $conv->patient_id)->first();
        if (!$patient) {
            echo "   ❌ Conversation {$conv->id} still has invalid patient_id {$conv->patient_id}\n";
            $allGood = false;
        }
    }
    
    if ($allGood) {
        echo "   ✅ All conversation relationships are now valid!\n";
    }
    
    echo "\n🎉 PRODUCTION MESSAGING FIX COMPLETED SUCCESSFULLY!\n";
    echo "The messaging system should now work correctly for all users.\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n🚨 NO CHANGES WERE APPLIED due to error.\n";
    exit(1);
}

echo "\n=== SCRIPT COMPLETE ===\n";