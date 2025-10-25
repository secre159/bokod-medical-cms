<?php
// Database Configuration for Backlink Checker
define('DB_HOST', 'localhost');
define('DB_NAME', 'backlink_checker');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('MAX_REQUESTS_PER_HOUR', 100);
define('MAX_BULK_URLS', 50);
define('REQUEST_TIMEOUT', 30);
define('USER_AGENT', 'BacklinkChecker/1.0 (+https://your-domain.com)');

// API Keys (if using third-party services)
define('MOZ_ACCESS_ID', ''); // Add your Moz API credentials
define('MOZ_SECRET_KEY', '');
define('AHREFS_API_TOKEN', ''); // Add your Ahrefs API token
define('SEMRUSH_API_KEY', ''); // Add your SEMrush API key

// Feature Flags
define('ENABLE_GOOGLE_SEARCH', true);
define('ENABLE_DATABASE_STORAGE', true);
define('ENABLE_RATE_LIMITING', true);
define('ENABLE_API_LOGGING', true);

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query failed: " . $e->getMessage());
            throw new Exception("Database query failed");
        }
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $this->query($sql, array_merge($data, $whereParams));
        
        return $this->connection->lastInsertId();
    }
}

class RateLimiter {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function checkRateLimit($ip, $maxRequests = MAX_REQUESTS_PER_HOUR) {
        if (!ENABLE_RATE_LIMITING) {
            return true;
        }
        
        $sql = "SELECT COUNT(*) as request_count 
                FROM api_usage_logs 
                WHERE ip_address = :ip 
                AND request_timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $result = $this->db->fetchOne($sql, ['ip' => $ip]);
        
        return $result['request_count'] < $maxRequests;
    }
    
    public function logRequest($ip, $userAgent, $domain, $analysisType, $status = 'success', $errorMessage = null, $processingTime = null) {
        if (!ENABLE_API_LOGGING) {
            return;
        }
        
        $data = [
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'domain_analyzed' => $domain,
            'analysis_type' => $analysisType,
            'response_status' => $status,
            'error_message' => $errorMessage,
            'processing_time_ms' => $processingTime
        ];
        
        $this->db->insert('api_usage_logs', $data);
    }
}

class DomainManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getDomainId($domainName) {
        $sql = "SELECT id FROM domains WHERE domain_name = :domain";
        $result = $this->db->fetchOne($sql, ['domain' => $domainName]);
        
        if ($result) {
            return $result['id'];
        }
        
        // Create new domain
        return $this->db->insert('domains', ['domain_name' => $domainName]);
    }
    
    public function updateDomainMetrics($domainId, $da, $pa) {
        $this->db->update(
            'domains', 
            ['domain_authority' => $da, 'page_authority' => $pa],
            'id = :id',
            ['id' => $domainId]
        );
    }
    
    public function getDomainStats($domainName) {
        $sql = "SELECT * FROM backlink_summary WHERE domain_name = :domain";
        return $this->db->fetchOne($sql, ['domain' => $domainName]);
    }
}

class AnalysisManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function createAnalysis($domainId, $analysisType, $analysisData = null) {
        return $this->db->insert('analyses', [
            'domain_id' => $domainId,
            'analysis_type' => $analysisType,
            'analysis_data' => $analysisData ? json_encode($analysisData) : null,
            'status' => 'pending'
        ]);
    }
    
    public function updateAnalysisResults($analysisId, $results) {
        $data = [
            'total_backlinks' => $results['statistics']['totalBacklinks'] ?? 0,
            'dofollow_links' => $results['statistics']['dofollow'] ?? 0,
            'nofollow_links' => $results['statistics']['nofollow'] ?? 0,
            'unique_domains' => $results['statistics']['uniqueDomains'] ?? 0,
            'analysis_data' => json_encode($results),
            'status' => 'completed'
        ];
        
        $this->db->update('analyses', $data, 'id = :id', ['id' => $analysisId]);
    }
    
    public function markAnalysisFailed($analysisId, $errorMessage) {
        $this->db->update(
            'analyses',
            ['status' => 'failed', 'error_message' => $errorMessage],
            'id = :id',
            ['id' => $analysisId]
        );
    }
    
    public function saveBacklinks($analysisId, $backlinks) {
        foreach ($backlinks as $link) {
            $data = [
                'analysis_id' => $analysisId,
                'source_domain' => parse_url($link['sourceUrl'], PHP_URL_HOST) ?? '',
                'source_url' => $link['sourceUrl'],
                'target_domain' => parse_url($link['targetUrl'], PHP_URL_HOST) ?? '',
                'target_url' => $link['targetUrl'],
                'anchor_text' => $link['anchorText'] ?? '',
                'link_type' => $link['linkType'] ?? 'unknown',
                'domain_authority' => $link['domainAuthority'] ?? null,
                'status' => $link['status'] ?? 'unknown',
                'link_source' => $link['source'] ?? 'unknown'
            ];
            
            $this->db->insert('backlinks', $data);
        }
    }
    
    public function saveBulkCheckResults($analysisId, $results) {
        foreach ($results as $result) {
            $data = [
                'analysis_id' => $analysisId,
                'checked_url' => $result['url'],
                'has_backlink' => $result['hasBacklink'] ? 1 : 0,
                'link_count' => $result['linkCount'] ?? 0,
                'link_type' => $result['linkType'] ?? 'unknown',
                'domain_authority' => $result['domainAuthority'] ?? null,
                'anchor_texts' => isset($result['anchorTexts']) ? json_encode($result['anchorTexts']) : null,
                'check_status' => $result['status'] ?? 'checked',
                'error_message' => $result['error'] ?? null
            ];
            
            $this->db->insert('bulk_checks', $data);
        }
    }
}

// Utility functions
function sanitizeDomain($domain) {
    $domain = trim($domain);
    $domain = preg_replace('#^https?://#', '', $domain);
    $domain = preg_replace('#^www\.#', '', $domain);
    $domain = rtrim($domain, '/');
    return strtolower($domain);
}

function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function getClientIp() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            return trim($ips[0]);
        }
    }
    return '0.0.0.0';
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

// Error handler for production
function handleError($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $errorMsg = "Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}";
    error_log($errorMsg);
    
    // Don't expose internal errors to client
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error']);
        exit;
    }
    
    return true;
}

set_error_handler('handleError');

// Exception handler for uncaught exceptions
function handleException($exception) {
    error_log("Uncaught exception: " . $exception->getMessage());
    
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error']);
    }
    
    exit;
}

set_exception_handler('handleException');
?>