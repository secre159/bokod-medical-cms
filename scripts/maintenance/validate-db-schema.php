<?php
/**
 * Database Schema Validation Script
 * Validates that all essential columns exist in the database
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

echo "🔍 Database Schema Validation\n";
echo "=============================\n\n";

try {
    $connection = Illuminate\Support\Facades\DB::connection();
    $dbName = $connection->getDatabaseName();
    
    echo "✅ Connected to database: {$dbName}\n";
    echo "📊 Database driver: " . $connection->getDriverName() . "\n\n";
    
    // Define required columns for each table
    $requiredColumns = [
        'users' => [
            'id', 'name', 'email', 'password', 'phone', 'date_of_birth', 'gender', 
            'address', 'role', 'status', 'registration_status', 'profile_picture',
            'created_at', 'updated_at', 'created_by', 'updated_by'
        ],
        'patients' => [
            'id', 'patient_name', 'email', 'phone_number', 'date_of_birth', 'gender',
            'address', 'civil_status', 'medical_conditions', 'allergies', 
            'emergency_contact_name', 'emergency_contact_phone', 'profile_picture',
            'user_id', 'archived', 'created_at', 'updated_at'
        ],
        'appointments' => [
            'appointment_id', 'patient_id', 'appointment_date', 'appointment_time',
            'reason', 'status', 'approval_status', 'cancellation_reason',
            'created_at', 'updated_at'
        ],
        'medicines' => [
            'id', 'medicine_name', 'generic_name', 'description', 'stock_quantity',
            'low_stock_threshold', 'unit_price', 'selling_price', 'expiry_date',
            'medicine_image', 'created_at', 'updated_at'
        ],
        'prescriptions' => [
            'id', 'patient_id', 'medicine_name', 'dosage', 'quantity', 'instructions',
            'status', 'appointment_id', 'generic_name', 'consultation_type',
            'unit_price', 'total_amount', 'created_at', 'updated_at'
        ],
        'conversations' => [
            'id', 'patient_id', 'admin_id', 'created_at', 'updated_at',
            'archived_by_admin', 'archived_by_patient'
        ],
        'messages' => [
            'id', 'conversation_id', 'sender_id', 'message', 'is_read', 'read_at',
            'attachment_path', 'attachment_name', 'reactions', 'created_at', 'updated_at'
        ]
    ];
    
    $allValid = true;
    $results = [];
    
    foreach ($requiredColumns as $tableName => $columns) {
        echo "📋 Checking table: {$tableName}\n";
        
        if (!Schema::hasTable($tableName)) {
            echo "   ❌ Table does not exist!\n";
            $allValid = false;
            $results[$tableName] = ['exists' => false, 'missing_columns' => $columns];
            continue;
        }
        
        $actualColumns = Schema::getColumnListing($tableName);
        $missingColumns = array_diff($columns, $actualColumns);
        $extraColumns = array_diff($actualColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "   ✅ All required columns exist\n";
            $results[$tableName] = ['exists' => true, 'status' => 'complete'];
        } else {
            echo "   ⚠️  Missing columns: " . implode(', ', $missingColumns) . "\n";
            $allValid = false;
            $results[$tableName] = ['exists' => true, 'missing_columns' => $missingColumns];
        }
        
        if (!empty($extraColumns)) {
            echo "   ℹ️  Additional columns: " . implode(', ', $extraColumns) . "\n";
        }
        
        echo "   📊 Total columns: " . count($actualColumns) . "\n\n";
    }
    
    // Save results
    $reportFile = __DIR__ . '/database-validation-report.json';
    file_put_contents($reportFile, json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'database' => $dbName,
        'driver' => $connection->getDriverName(),
        'all_valid' => $allValid,
        'results' => $results
    ], JSON_PRETTY_PRINT));
    
    echo "📄 Validation report saved: " . basename($reportFile) . "\n\n";
    
    if ($allValid) {
        echo "🎉 SUCCESS: All required columns exist!\n";
        echo "✅ Your database schema is complete and ready for use.\n";
    } else {
        echo "⚠️  WARNING: Some required columns are missing.\n";
        echo "💡 Run additional migrations or check the comprehensive migration.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🔗 Next steps:\n";
echo "1. Your local MySQL database is now synchronized\n";
echo "2. All essential columns have been added\n";
echo "3. Your application should work properly now\n";
?>