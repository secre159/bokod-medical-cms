-- Reset Migrations Table
-- This removes all old migration records and prepares for the new comprehensive migration

-- Clear all existing migration records
DELETE FROM migrations;

-- Insert only the Laravel default migrations (if they exist in your database)
-- These are the standard Laravel framework tables

-- Note: After running this, you'll need to run: php artisan migrate
-- This will record the new comprehensive migration in the migrations table
