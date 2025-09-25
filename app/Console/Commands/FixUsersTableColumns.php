<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixUsersTableColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-users-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing columns in users table for production deployment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking users table structure...');

        try {
            // Check if columns exist and add them if they don't
            $columnsToAdd = [
                'role' => "VARCHAR(50) DEFAULT 'patient'",
                'status' => "VARCHAR(50) DEFAULT 'active'",
                'registration_status' => "VARCHAR(50) DEFAULT 'approved'",
                'approved_at' => "TIMESTAMP NULL",
                'approved_by' => "BIGINT NULL",
                'rejection_reason' => "TEXT NULL",
                'registration_source' => "VARCHAR(50) DEFAULT 'admin'",
                'profile_picture' => "VARCHAR(255) NULL",
                'display_name' => "VARCHAR(255) NULL"
            ];

            foreach ($columnsToAdd as $column => $definition) {
                if (!Schema::hasColumn('users', $column)) {
                    $this->info("Adding missing column: {$column}");
                    DB::statement("ALTER TABLE users ADD COLUMN {$column} {$definition}");
                } else {
                    $this->info("Column {$column} already exists, skipping...");
                }
            }

            // Add check constraints for enum-like columns
            $this->info('Adding check constraints...');
            
            try {
                DB::statement("ALTER TABLE users ADD CONSTRAINT chk_users_registration_status CHECK (registration_status IN ('pending', 'approved', 'rejected'))");
            } catch (\Exception $e) {
                // Constraint might already exist
                $this->warn('Registration status constraint might already exist: ' . $e->getMessage());
            }

            try {
                DB::statement("ALTER TABLE users ADD CONSTRAINT chk_users_registration_source CHECK (registration_source IN ('admin', 'self', 'import'))");
            } catch (\Exception $e) {
                // Constraint might already exist
                $this->warn('Registration source constraint might already exist: ' . $e->getMessage());
            }

            // Add foreign key for approved_by
            try {
                if (Schema::hasColumn('users', 'approved_by')) {
                    DB::statement('ALTER TABLE users ADD CONSTRAINT fk_users_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL');
                }
            } catch (\Exception $e) {
                $this->warn('Foreign key constraint might already exist: ' . $e->getMessage());
            }

            // Add indexes
            $this->info('Adding indexes...');
            $indexes = ['role', 'status', 'registration_status'];
            foreach ($indexes as $column) {
                try {
                    DB::statement("CREATE INDEX IF NOT EXISTS idx_users_{$column} ON users({$column})");
                } catch (\Exception $e) {
                    $this->warn("Index for {$column} might already exist: " . $e->getMessage());
                }
            }

            $this->info('Users table structure fixed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to fix users table: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}