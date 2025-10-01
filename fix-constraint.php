<?php
/**
 * EMERGENCY PostgreSQL Constraint Fix Script
 * Run this script to fix the constraint issue directly
 * 
 * Usage: php fix-constraint.php
 */

// Database connection details
$DATABASE_URL = "postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms";

echo "🔧 EMERGENCY PostgreSQL Constraint Fix\n";
echo "=====================================\n\n";

try {
    // Parse the DATABASE_URL
    $url_parts = parse_url($DATABASE_URL);
    $host = $url_parts['host'];
    $port = $url_parts['port'] ?? 5432;
    $dbname = ltrim($url_parts['path'], '/');
    $user = $url_parts['user'];
    $password = $url_parts['pass'];
    
    echo "📋 Connection Details:\n";
    echo "   Host: $host\n";
    echo "   Database: $dbname\n";
    echo "   User: $user\n\n";
    
    // Create PDO connection
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10
    ]);
    
    echo "✅ Successfully connected to PostgreSQL database!\n\n";
    
    // Check current appointment statuses
    echo "📊 Checking current appointment statuses...\n";
    $stmt = $pdo->query("SELECT DISTINCT status FROM appointments ORDER BY status");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "   Current statuses: " . implode(', ', $statuses) . "\n\n";
    
    // Drop existing constraint
    echo "🔨 Dropping existing constraint...\n";
    try {
        $pdo->exec("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
        echo "   ✅ Existing constraint dropped successfully\n";
    } catch (PDOException $e) {
        echo "   ⚠️  Drop constraint warning: " . $e->getMessage() . "\n";
    }
    
    // Add new constraint with overdue status
    echo "\n🏗️  Adding new constraint with 'overdue' status...\n";
    $sql = "ALTER TABLE appointments ADD CONSTRAINT appointments_status_check 
            CHECK (status IN ('pending', 'active', 'completed', 'cancelled', 'overdue'))";
    
    $pdo->exec($sql);
    echo "   ✅ New constraint added successfully!\n\n";
    
    // Verify constraint exists
    echo "🔍 Verifying constraint...\n";
    $stmt = $pdo->query("SELECT conname FROM pg_constraint WHERE conname = 'appointments_status_check'");
    $constraint = $stmt->fetch();
    
    if ($constraint) {
        echo "   ✅ Constraint verification successful!\n\n";
    } else {
        echo "   ❌ Constraint verification failed!\n\n";
        throw new Exception("Constraint was not created properly");
    }
    
    // Now update the specific overdue appointment
    echo "🕐 Updating overdue appointments...\n";
    
    // Find overdue appointments
    $stmt = $pdo->query("
        SELECT appointment_id, appointment_date, appointment_time, status 
        FROM appointments 
        WHERE status = 'active' 
        AND (appointment_date < CURRENT_DATE 
             OR (appointment_date = CURRENT_DATE AND appointment_time::time < CURRENT_TIME))
    ");
    
    $overdueAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($overdueAppointments)) {
        echo "   ℹ️  No overdue appointments found\n\n";
    } else {
        echo "   📋 Found " . count($overdueAppointments) . " overdue appointment(s):\n";
        
        foreach ($overdueAppointments as $appointment) {
            echo "      - ID {$appointment['appointment_id']}: {$appointment['appointment_date']} {$appointment['appointment_time']}\n";
            
            // Update to overdue status
            $updateStmt = $pdo->prepare("
                UPDATE appointments 
                SET status = 'overdue', updated_at = CURRENT_TIMESTAMP 
                WHERE appointment_id = ?
            ");
            
            $updateStmt->execute([$appointment['appointment_id']]);
            echo "        ✅ Updated to 'overdue' status\n";
        }
    }
    
    echo "\n🎉 ALL DONE! PostgreSQL constraint has been fixed!\n";
    echo "   The website should now work properly without errors.\n";
    echo "   Overdue appointments will display with red 'Overdue' badges.\n\n";
    echo "🌐 Visit your website: https://bokod-medical-cms.onrender.com\n\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "\n💡 Troubleshooting:\n";
    echo "   1. Check if your database is accessible\n";
    echo "   2. Verify the DATABASE_URL is correct\n";
    echo "   3. Make sure you have necessary permissions\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}
?>