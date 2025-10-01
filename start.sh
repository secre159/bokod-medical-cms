#!/bin/bash
set -e

echo "=== STARTING LARAVEL DEPLOYMENT ==="
echo "Working directory: $(pwd)"
echo "Database URL: ${DATABASE_URL:0:20}..."
echo "Port: $PORT"

# Copy production environment file if it exists
if [ -f ".env.production" ]; then
    echo "Using production environment configuration..."
    cp .env.production .env
fi

# Wait for database connection
echo "Waiting for database connection..."
for i in {1..30}; do
    if php artisan migrate:status >/dev/null 2>&1; then
        echo "Database connected successfully!"
        break
    fi
    echo "Attempt $i: Database not ready, waiting 3 seconds..."
    sleep 3
done

# Force run all migrations
echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force --verbose
echo "=== MIGRATION STATUS ==="
php artisan migrate:status

# Run additional PostgreSQL constraint fix if needed
echo "=== RUNNING POSTGRESQL CONSTRAINT FIX ==="
php artisan db:show
echo "Database driver: $(php artisan tinker --execute='echo DB::getDriverName()')"
echo "Current appointment statuses: $(php artisan tinker --execute='echo DB::table("appointments")->distinct()->pluck("status")->implode(", ")')"

# CRITICAL: PostgreSQL constraint fix - multiple attempts
echo "Running aggressive PostgreSQL constraint fix..."

# Method 1: Direct SQL via psql (if available)
if [ -n "$DATABASE_URL" ]; then
    echo "Attempting direct SQL fix via DATABASE_URL..."
    echo "ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check; ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN ('pending', 'active', 'completed', 'cancelled', 'overdue'));" | psql "$DATABASE_URL" || echo "Direct SQL failed, trying PHP method..."
fi

# Method 2: PHP script with multiple attempts
echo "Running PHP constraint fix..."
php -r '
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    $app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
    
    try {
        if (DB::getDriverName() === "pgsql") {
            echo "PostgreSQL detected, fixing constraint...\n";
            
            // Drop constraint with multiple attempts
            $dropQueries = [
                "ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check",
                "ALTER TABLE appointments DROP CONSTRAINT appointments_status_check",
            ];
            
            foreach ($dropQueries as $query) {
                try {
                    DB::statement($query);
                    echo "Successfully dropped constraint\n";
                    break;
                } catch (Exception $e) {
                    echo "Drop attempt failed: " . $e->getMessage() . "\n";
                }
            }
            
            // Add new constraint
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN (\"pending\", \"active\", \"completed\", \"cancelled\", \"overdue\"))");
            echo "Added new constraint with overdue status\n";
            
            // Verify constraint exists
            $result = DB::select("SELECT conname FROM pg_constraint WHERE conname = \"appointments_status_check\"");
            if (!empty($result)) {
                echo "✓ Constraint verified successfully!\n";
            } else {
                echo "✗ Constraint verification failed!\n";
            }
        } else {
            echo "Not PostgreSQL, skipping constraint fix\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Attempting emergency constraint fix...\n";
        
        // Emergency fallback
        try {
            DB::unprepared("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check;");
            DB::unprepared("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN (\"pending\", \"active\", \"completed\", \"cancelled\", \"overdue\"));");
            echo "Emergency constraint fix completed\n";
        } catch (Exception $e2) {
            echo "Emergency fix also failed: " . $e2->getMessage() . "\n";
        }
    }
'

# Method 3: Manual constraint fix command
echo "Running manual constraint fix command..."
php artisan postgresql:fix-constraint || echo "Manual command not available"

# Clear all caches
echo "=== CLEARING CACHES ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "=== STARTING LARAVEL SERVER ==="
exec php artisan serve --host=0.0.0.0 --port=$PORT