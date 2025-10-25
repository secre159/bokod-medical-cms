-- Backlink Checker Database Schema
-- This script creates tables for storing backlink analysis data

CREATE TABLE IF NOT EXISTS domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_name VARCHAR(255) NOT NULL UNIQUE,
    domain_authority INT DEFAULT NULL,
    page_authority INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_domain_name (domain_name)
);

CREATE TABLE IF NOT EXISTS analyses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    analysis_type ENUM('basic', 'bulk', 'competitor', 'monitor', 'google_search', 'verify_links') DEFAULT 'basic',
    total_backlinks INT DEFAULT 0,
    dofollow_links INT DEFAULT 0,
    nofollow_links INT DEFAULT 0,
    unique_domains INT DEFAULT 0,
    analysis_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    analysis_data JSON,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    error_message TEXT,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE,
    INDEX idx_domain_analysis (domain_id, analysis_date),
    INDEX idx_analysis_type (analysis_type),
    INDEX idx_status (status)
);

CREATE TABLE IF NOT EXISTS backlinks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analysis_id INT NOT NULL,
    source_domain VARCHAR(255) NOT NULL,
    source_url TEXT NOT NULL,
    target_domain VARCHAR(255) NOT NULL,
    target_url TEXT NOT NULL,
    anchor_text TEXT,
    link_type ENUM('dofollow', 'nofollow', 'unknown') DEFAULT 'unknown',
    domain_authority INT DEFAULT NULL,
    page_authority INT DEFAULT NULL,
    first_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'broken', 'redirected', 'unknown') DEFAULT 'unknown',
    link_source VARCHAR(100) DEFAULT 'unknown', -- google_search, manual_check, etc.
    notes TEXT,
    FOREIGN KEY (analysis_id) REFERENCES analyses(id) ON DELETE CASCADE,
    INDEX idx_source_domain (source_domain),
    INDEX idx_target_domain (target_domain),
    INDEX idx_link_type (link_type),
    INDEX idx_status (status),
    INDEX idx_first_seen (first_seen),
    INDEX idx_analysis_backlinks (analysis_id)
);

CREATE TABLE IF NOT EXISTS bulk_checks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analysis_id INT NOT NULL,
    checked_url TEXT NOT NULL,
    has_backlink BOOLEAN DEFAULT FALSE,
    link_count INT DEFAULT 0,
    link_type ENUM('dofollow', 'nofollow', 'mixed', 'unknown') DEFAULT 'unknown',
    domain_authority INT DEFAULT NULL,
    anchor_texts JSON,
    check_status ENUM('checked', 'error', 'timeout') DEFAULT 'checked',
    error_message TEXT,
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (analysis_id) REFERENCES analyses(id) ON DELETE CASCADE,
    INDEX idx_bulk_analysis (analysis_id),
    INDEX idx_has_backlink (has_backlink),
    INDEX idx_check_status (check_status)
);

CREATE TABLE IF NOT EXISTS competitor_analysis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analysis_id INT NOT NULL,
    competitor_domain VARCHAR(255) NOT NULL,
    competitor_backlinks INT DEFAULT 0,
    common_backlinks INT DEFAULT 0,
    competitor_da INT DEFAULT NULL,
    competitor_pa INT DEFAULT NULL,
    analysis_notes TEXT,
    analyzed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (analysis_id) REFERENCES analyses(id) ON DELETE CASCADE,
    INDEX idx_competitor_analysis (analysis_id),
    INDEX idx_competitor_domain (competitor_domain)
);

CREATE TABLE IF NOT EXISTS monitoring_changes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    change_type ENUM('new', 'lost', 'changed', 'status_change') NOT NULL,
    backlink_id INT NULL, -- NULL for new backlinks that don't exist yet
    old_value TEXT,
    new_value TEXT,
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE,
    FOREIGN KEY (backlink_id) REFERENCES backlinks(id) ON DELETE SET NULL,
    INDEX idx_monitoring_domain (domain_id, change_date),
    INDEX idx_change_type (change_type),
    INDEX idx_change_date (change_date)
);

CREATE TABLE IF NOT EXISTS api_usage_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45),
    user_agent TEXT,
    domain_analyzed VARCHAR(255),
    analysis_type VARCHAR(50),
    request_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processing_time_ms INT DEFAULT NULL,
    response_status ENUM('success', 'error', 'timeout') DEFAULT 'success',
    error_message TEXT,
    INDEX idx_ip_timestamp (ip_address, request_timestamp),
    INDEX idx_domain_analyzed (domain_analyzed),
    INDEX idx_response_status (response_status)
);

-- Create views for reporting
CREATE VIEW backlink_summary AS
SELECT 
    d.domain_name,
    COUNT(DISTINCT a.id) as total_analyses,
    MAX(a.analysis_date) as last_analysis,
    COALESCE(MAX(a.total_backlinks), 0) as latest_backlink_count,
    COALESCE(MAX(a.dofollow_links), 0) as latest_dofollow_count,
    COALESCE(MAX(a.nofollow_links), 0) as latest_nofollow_count,
    COALESCE(MAX(a.unique_domains), 0) as latest_unique_domains,
    d.domain_authority,
    d.page_authority
FROM domains d
LEFT JOIN analyses a ON d.id = a.domain_id
GROUP BY d.id, d.domain_name, d.domain_authority, d.page_authority;

CREATE VIEW recent_backlinks AS
SELECT 
    d.domain_name as target_domain,
    b.source_domain,
    b.source_url,
    b.anchor_text,
    b.link_type,
    b.domain_authority,
    b.status,
    b.first_seen,
    b.last_seen,
    a.analysis_type
FROM backlinks b
JOIN analyses a ON b.analysis_id = a.id
JOIN domains d ON a.domain_id = d.id
WHERE b.first_seen >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY b.first_seen DESC;

CREATE VIEW monitoring_summary AS
SELECT 
    d.domain_name,
    mc.change_type,
    COUNT(*) as change_count,
    MAX(mc.change_date) as latest_change
FROM monitoring_changes mc
JOIN domains d ON mc.domain_id = d.id
WHERE mc.change_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY d.id, d.domain_name, mc.change_type
ORDER BY latest_change DESC;

-- Insert some sample data for testing (optional)
INSERT IGNORE INTO domains (domain_name, domain_authority, page_authority) VALUES
('example.com', 65, 70),
('testsite.org', 45, 50),
('demo-blog.net', 35, 40);

-- Create indexes for better performance
CREATE INDEX idx_backlinks_composite ON backlinks(target_domain, status, link_type);
CREATE INDEX idx_analyses_date_type ON analyses(analysis_date, analysis_type);
CREATE INDEX idx_bulk_checks_composite ON bulk_checks(has_backlink, check_status);

-- Add triggers for automatic timestamp updates
DELIMITER //

CREATE TRIGGER update_domain_timestamp 
    BEFORE UPDATE ON domains
    FOR EACH ROW
    BEGIN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END//

CREATE TRIGGER update_backlink_last_seen 
    BEFORE UPDATE ON backlinks
    FOR EACH ROW
    BEGIN
        IF NEW.status != OLD.status OR NEW.anchor_text != OLD.anchor_text THEN
            SET NEW.last_seen = CURRENT_TIMESTAMP;
        END IF;
    END//

DELIMITER ;

-- Create stored procedures for common operations
DELIMITER //

CREATE PROCEDURE GetDomainBacklinkSummary(IN domain_name VARCHAR(255))
BEGIN
    SELECT 
        COUNT(DISTINCT b.id) as total_backlinks,
        COUNT(DISTINCT CASE WHEN b.link_type = 'dofollow' THEN b.id END) as dofollow_count,
        COUNT(DISTINCT CASE WHEN b.link_type = 'nofollow' THEN b.id END) as nofollow_count,
        COUNT(DISTINCT b.source_domain) as unique_referring_domains,
        AVG(b.domain_authority) as avg_referring_da,
        COUNT(DISTINCT CASE WHEN b.status = 'broken' THEN b.id END) as broken_links,
        MAX(b.first_seen) as latest_backlink_discovered
    FROM domains d
    JOIN analyses a ON d.id = a.domain_id
    JOIN backlinks b ON a.id = b.analysis_id
    WHERE d.domain_name = domain_name;
END//

CREATE PROCEDURE GetRecentChanges(IN domain_name VARCHAR(255), IN days_back INT)
BEGIN
    SELECT 
        mc.change_type,
        mc.description,
        mc.change_date,
        b.source_url,
        b.anchor_text
    FROM monitoring_changes mc
    JOIN domains d ON mc.domain_id = d.id
    LEFT JOIN backlinks b ON mc.backlink_id = b.id
    WHERE d.domain_name = domain_name 
    AND mc.change_date >= DATE_SUB(NOW(), INTERVAL days_back DAY)
    ORDER BY mc.change_date DESC;
END//

DELIMITER ;