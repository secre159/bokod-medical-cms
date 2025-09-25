-- SQL commands to fix the users table in production PostgreSQL
-- Run these commands in your PostgreSQL database

-- Add the missing columns if they don't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) DEFAULT 'patient';
ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'active';
ALTER TABLE users ADD COLUMN IF NOT EXISTS registration_status VARCHAR(50) DEFAULT 'approved' CHECK (registration_status IN ('pending', 'approved', 'rejected'));
ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_by BIGINT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS registration_source VARCHAR(50) DEFAULT 'admin' CHECK (registration_source IN ('admin', 'self', 'import'));
ALTER TABLE users ADD COLUMN IF NOT EXISTS display_name VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL;

-- Add foreign key constraint for approved_by (if it doesn't exist)
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_users_approved_by' 
        AND table_name = 'users'
    ) THEN
        ALTER TABLE users ADD CONSTRAINT fk_users_approved_by 
        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;
    END IF;
END $$;

-- Add indexes for performance
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_users_registration_status ON users(registration_status);

-- Verify the table structure
SELECT column_name, data_type, is_nullable, column_default 
FROM information_schema.columns 
WHERE table_name = 'users' 
ORDER BY ordinal_position;