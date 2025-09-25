-- SQL script to manually add missing columns to users table
-- This should be run on the production PostgreSQL database

-- Add missing columns to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS role VARCHAR(50) DEFAULT 'patient',
ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'active',
ADD COLUMN IF NOT EXISTS registration_status VARCHAR(50) DEFAULT 'approved' CHECK (registration_status IN ('pending', 'approved', 'rejected')),
ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS approved_by BIGINT NULL,
ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL,
ADD COLUMN IF NOT EXISTS registration_source VARCHAR(50) DEFAULT 'admin' CHECK (registration_source IN ('admin', 'self', 'import')),
ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS display_name VARCHAR(255) NULL;

-- Add foreign key constraint for approved_by
ALTER TABLE users 
ADD CONSTRAINT fk_users_approved_by 
FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;

-- Add indexes for performance
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_users_registration_status ON users(registration_status);

-- Update comments
COMMENT ON COLUMN users.registration_status IS 'Status of patient self-registration';
COMMENT ON COLUMN users.approved_at IS 'When admin approved the registration';
COMMENT ON COLUMN users.approved_by IS 'Admin who approved the registration';
COMMENT ON COLUMN users.rejection_reason IS 'Reason for rejecting registration';
COMMENT ON COLUMN users.registration_source IS 'How the user was registered';