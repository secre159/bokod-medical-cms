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

# Explicit constraint fix command
echo "Running explicit constraint fix..."
php -r '
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    $app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
    
    try {
        if (DB::getDriverName() === "pgsql") {
            DB::statement("ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_status_check");
            echo "Dropped existing constraint\n";
            
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_status_check CHECK (status IN (\"pending\", \"active\", \"completed\", \"cancelled\", \"overdue\"))");
            echo "Added new constraint with overdue status\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
'

# Clear all caches
echo "=== CLEARING CACHES ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "=== STARTING LARAVEL SERVER ==="
exec php artisan serve --host=0.0.0.0 --port=$PORT