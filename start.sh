#!/bin/bash
set -e

echo "=== STARTING LARAVEL DEPLOYMENT ==="
echo "Working directory: $(pwd)"
echo "Database URL: ${DATABASE_URL:0:20}..."
echo "Port: $PORT"

# If a command is provided as arguments (e.g., Render Start Command / Cron Job), execute it directly
if [ "$#" -gt 0 ]; then
    echo "Command args detected. Executing: $*"
    exec "$@"
fi

# If this container is started for a worker/cron job, honor WORKER_COMMAND
if [ -n "${WORKER_COMMAND:-}" ]; then
    echo "Worker mode detected. Executing WORKER_COMMAND: $WORKER_COMMAND"
    exec bash -lc "$WORKER_COMMAND"
fi

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


# Clear all caches
echo "=== CLEARING CACHES ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "=== STARTING LARAVEL SERVER ==="
exec php artisan serve --host=0.0.0.0 --port=$PORT