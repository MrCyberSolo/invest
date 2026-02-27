<?php
/**
 * File Manager Pro - Secure Edition
 * Enhanced security features including:
 * - Rate limiting for login attempts
 * - Session fingerprinting
 * - Security headers
 * - Audit logging
 * - External configuration
 * - MIME type verification
 */

// =====================================================
// SECURITY HEADERS - Set before any output
// =====================================================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data: blob:; connect-src 'self';");

// =====================================================
// CONFIGURATION
// =====================================================
define('FM_CONFIG_FILE', __DIR__ . '/.fm_config.json');
define('FM_AUDIT_LOG', __DIR__ . '/.fm_audit.log');
define('FM_LOGIN_ATTEMPTS_FILE', __DIR__ . '/.fm_login_attempts.json');

// Security settings
define('FM_MAX_LOGIN_ATTEMPTS', 5);
define('FM_LOCKOUT_TIME', 900); // 15 minutes
define('FM_SESSION_FINGERPRINT', true);

// Load external config if exists, otherwise use defaults
function loadConfig(): array {
    $defaults = [
        'users' => [
            'admin' => [
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'root' => '/',
                'is_admin' => true,
                'must_change_password' => true,
            ],
        ],
        'max_upload_size' => 100 * 1024 * 1024,
        'max_edit_size' => 5 * 1024 * 1024,
        'max_preview_size' => 25 * 1024 * 1024,
        'allowed_extensions' => [],
        'blocked_extensions' => ['exe', 'bat', 'cmd', 'sh', 'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'phps', 'cgi', 'pl', 'asp', 'aspx', 'jsp'],
        'ip_whitelist' => [],
        'ip_blacklist' => [],
        'session_timeout' => 3600,
        'excluded_folders' => ['.git', 'node_modules', 'vendor', '.svn'],
        'excluded_files' => ['.DS_Store', 'Thumbs.db', '.fm_config.json', '.fm_audit.log', '.fm_login_attempts.json', '.fm_users.json', '.fm_shares.json'],
        'show_hidden' => false,
        'base_url' => '',
        'editor_theme' => 'monokai',
        'editor_font_size' => 14,
        'share_enabled' => true,
        'share_data_file' => __DIR__ . '/.fm_shares.json',
        'share_expiry_options' => [
            '1h' => '1 hour',
            '24h' => '24 hours',
            '7d' => '7 days',
            '30d' => '30 days',
            'never' => 'Never expires',
        ],
        'users_data_file' => __DIR__ . '/.fm_users.json',
        'audit_enabled' => true,
        'require_strong_passwords' => true,
        'min_password_length' => 8,
    ];
    
    if (file_exists(FM_CONFIG_FILE)) {
        $external = @json_decode(file_get_contents(FM_CONFIG_FILE), true);
        if ($external) {
            return array_merge($defaults, $external);
        }
    }
    
    return $defaults;
}

$CONFIG = loadConfig();

// =====================================================
// AUDIT LOGGING FUNCTIONS
// =====================================================
function auditLog(string $action, string $details = '', string $level = 'INFO'): void {
    global $CONFIG;
    if (!($CONFIG['audit_enabled'] ?? true)) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user = $_SESSION['username'] ?? 'anonymous';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 100);
    
    $logEntry = sprintf(
        "[%s] [%s] [IP: %s] [User: %s] [Action: %s] %s | UA: %s\n",
        $timestamp,
        $level,
        $ip,
        $user,
        $action,
        $details,
        $userAgent
    );
    
    // Rotate log if too large (> 10MB)
    if (file_exists(FM_AUDIT_LOG) && filesize(FM_AUDIT_LOG) > 10 * 1024 * 1024) {
        $backupName = FM_AUDIT_LOG . '.' . date('Y-m-d_H-i-s') . '.bak';
        rename(FM_AUDIT_LOG, $backupName);
    }
    
    @file_put_contents(FM_AUDIT_LOG, $logEntry, FILE_APPEND | LOCK_EX);
}

// =====================================================
// RATE LIMITING FUNCTIONS
// =====================================================
function getLoginAttempts(): array {
    if (!file_exists(FM_LOGIN_ATTEMPTS_FILE)) {
        return [];
    }
    $data = @file_get_contents(FM_LOGIN_ATTEMPTS_FILE);
    return $data ? json_decode($data, true) ?? [] : [];
}

function saveLoginAttempts(array $attempts): void {
    file_put_contents(FM_LOGIN_ATTEMPTS_FILE, json_encode($attempts), LOCK_EX);
    @chmod(FM_LOGIN_ATTEMPTS_FILE, 0600);
}

function isIPLocked(string $ip): bool {
    $attempts = getLoginAttempts();
    if (!isset($attempts[$ip])) {
        return false;
    }
    
    $record = $attempts[$ip];
    
    // Clean up old attempts (older than lockout time)
    if (isset($record['locked_until']) && time() > $record['locked_until']) {
        unset($attempts[$ip]);
        saveLoginAttempts($attempts);
        return false;
    }
    
    return isset($record['locked_until']) && time() < $record['locked_until'];
}

function recordLoginAttempt(string $ip, bool $success): void {
    $attempts = getLoginAttempts();
    
    if ($success) {
        unset($attempts[$ip]);
        saveLoginAttempts($attempts);
        return;
    }
    
    if (!isset($attempts[$ip])) {
        $attempts[$ip] = ['count' => 0, 'first_attempt' => time()];
    }
    
    $attempts[$ip]['count']++;
    $attempts[$ip]['last_attempt'] = time();
    
    // Lock if too many attempts
    if ($attempts[$ip]['count'] >= FM_MAX_LOGIN_ATTEMPTS) {
        $attempts[$ip]['locked_until'] = time() + FM_LOCKOUT_TIME;
        auditLog('LOGIN_LOCKOUT', "IP locked due to " . FM_MAX_LOGIN_ATTEMPTS . " failed attempts", 'WARN');
    }
    
    saveLoginAttempts($attempts);
}

function getRemainingLockoutTime(string $ip): int {
    $attempts = getLoginAttempts();
    if (!isset($attempts[$ip]['locked_until'])) {
        return 0;
    }
    return max(0, $attempts[$ip]['locked_until'] - time());
}

// =====================================================
// SESSION SECURITY
// =====================================================
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);

session_name('fm_secure_' . substr(md5(__DIR__), 0, 8));
session_start();

// Generate session fingerprint
function generateSessionFingerprint(): string {
    $components = [
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        // Don't include IP to allow for mobile users changing networks
    ];
    return hash('sha256', implode('|', $components));
}

// Validate session fingerprint
function validateSessionFingerprint(): bool {
    if (!FM_SESSION_FINGERPRINT) return true;
    
    if (!isset($_SESSION['fingerprint'])) {
        $_SESSION['fingerprint'] = generateSessionFingerprint();
        return true;
    }
    
    return hash_equals($_SESSION['fingerprint'], generateSessionFingerprint());
}

// Initialize session security
if (!validateSessionFingerprint()) {
    auditLog('SESSION_HIJACK_ATTEMPT', 'Session fingerprint mismatch detected', 'CRITICAL');
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (empty($CONFIG['base_url'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $CONFIG['base_url'] = $protocol . '://' . $host . $script;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Session timeout check
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $CONFIG['session_timeout'])) {
    auditLog('SESSION_TIMEOUT', 'Session expired due to inactivity');
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
$_SESSION['last_activity'] = time();

// Regenerate session ID periodically (every 30 minutes)
if (!isset($_SESSION['created_at'])) {
    $_SESSION['created_at'] = time();
} elseif (time() - $_SESSION['created_at'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created_at'] = time();
}

function checkIPAccess(array $config): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    foreach ($config['ip_blacklist'] as $blocked) {
        if (ipMatch($ip, $blocked)) {
            return false;
        }
    }
    if (!empty($config['ip_whitelist'])) {
        foreach ($config['ip_whitelist'] as $allowed) {
            if (ipMatch($ip, $allowed)) {
                return true;
            }
        }
        return false;
    }
    return true;
}

function ipMatch(string $ip, string $range): bool {
    if (strpos($range, '/') === false) {
        return $ip === $range;
    }
    list($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    return ($ip & $mask) === ($subnet & $mask);
}

function isLoggedIn(): bool {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

function getDynamicUsers(): array {
    global $CONFIG;
    $file = $CONFIG['users_data_file'];
    if (!file_exists($file)) {
        return [];
    }
    $data = @file_get_contents($file);
    return $data ? json_decode($data, true) ?? [] : [];
}

function saveDynamicUsers(array $users): bool {
    global $CONFIG;
    return file_put_contents($CONFIG['users_data_file'], json_encode($users, JSON_PRETTY_PRINT)) !== false;
}

function getAllUsers(): array {
    global $CONFIG;
    $dynamicUsers = getDynamicUsers();
    return array_merge($CONFIG['users'], $dynamicUsers);
}

function getCurrentUser(): ?array {
    if (!isLoggedIn() || !isset($_SESSION['username'])) {
        return null;
    }
    $allUsers = getAllUsers();
    return $allUsers[$_SESSION['username']] ?? null;
}

function getUserRoot(): string {
    $user = getCurrentUser();
    return $user ? $user['root'] : __DIR__;
}

function isAdmin(): bool {
    $user = getCurrentUser();
    return $user && ($user['is_admin'] ?? false);
}

function createUser(string $username, string $password, string $root, bool $isAdmin): bool {
    $users = getDynamicUsers();
    if (isset($users[$username]) || isset($GLOBALS['CONFIG']['users'][$username])) {
        return false;
    }
    $users[$username] = [
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'root' => $root,
        'is_admin' => $isAdmin,
    ];
    return saveDynamicUsers($users);
}

function updateUser(string $username, ?string $password, ?string $root, ?bool $isAdmin): bool {
    global $CONFIG;
    $users = getDynamicUsers();
    $existsInDynamic = isset($users[$username]);
    $existsInConfig = isset($CONFIG['users'][$username]);
    if (!$existsInDynamic && !$existsInConfig) {
        return false;
    }
    if (!$existsInDynamic && $existsInConfig) {
        $users[$username] = $CONFIG['users'][$username];
    }
    if ($password !== null && $password !== '') {
        $users[$username]['password'] = password_hash($password, PASSWORD_DEFAULT);
    }
    if ($root !== null) {
        $users[$username]['root'] = $root;
    }
    if ($isAdmin !== null) {
        $users[$username]['is_admin'] = $isAdmin;
    }
    return saveDynamicUsers($users);
}

function deleteUser(string $username): bool {
    global $CONFIG;
    $users = getDynamicUsers();
    if (!isset($users[$username]) && isset($CONFIG['users'][$username])) {
        $users[$username] = null;
        return saveDynamicUsers($users);
    }
    if (isset($users[$username])) {
        unset($users[$username]);
        return saveDynamicUsers($users);
    }
    return false;
}

function getActiveUsers(): array {
    global $CONFIG;
    $dynamicUsers = getDynamicUsers();
    $allUsers = [];
    foreach ($CONFIG['users'] as $username => $user) {
        if (!array_key_exists($username, $dynamicUsers) || $dynamicUsers[$username] !== null) {
            $allUsers[$username] = isset($dynamicUsers[$username]) ? $dynamicUsers[$username] : $user;
            $allUsers[$username]['source'] = isset($dynamicUsers[$username]) ? 'modified' : 'default';
        }
    }
    foreach ($dynamicUsers as $username => $user) {
        if ($user !== null && !isset($CONFIG['users'][$username])) {
            $allUsers[$username] = $user;
            $allUsers[$username]['source'] = 'custom';
        }
    }
    return $allUsers;
}

function verifyCSRF(): bool {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function sanitizePath(string $path): string {
    $path = str_replace(["\0", "\\"], ['', '/'], $path);
    $parts = explode('/', $path);
    $safe = [];
    foreach ($parts as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') {
            array_pop($safe);
        } else {
            $safe[] = $part;
        }
    }
    return implode('/', $safe);
}

function getFullPath(string $relativePath): string {
    $root = getUserRoot();
    $sanitized = sanitizePath($relativePath);
    $fullPath = $root . ($sanitized ? '/' . $sanitized : '');
    $realRoot = realpath($root);
    $realPath = realpath($fullPath);
    if ($realPath === false) {
        $parent = dirname($fullPath);
        $realParent = realpath($parent);
        if ($realParent === false || strpos($realParent, $realRoot) !== 0) {
            return $realRoot;
        }
        return $fullPath;
    }
    if (strpos($realPath, $realRoot) !== 0) {
        return $realRoot;
    }
    return $realPath;
}

function isExtensionAllowed(string $filename): bool {
    global $CONFIG;
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (isAdmin()) {
        return true;
    }
    if (in_array($ext, $CONFIG['blocked_extensions'])) {
        return false;
    }
    if (!empty($CONFIG['allowed_extensions'])) {
        return in_array($ext, $CONFIG['allowed_extensions']);
    }
    return true;
}

function shouldExclude(string $name, bool $isDir): bool {
    global $CONFIG;
    $showHidden = $_SESSION['show_hidden'] ?? $CONFIG['show_hidden'];
    if (!$showHidden && strpos($name, '.') === 0) {
        return true;
    }
    if ($isDir) {
        return in_array($name, $CONFIG['excluded_folders']);
    }
    return in_array($name, $CONFIG['excluded_files']);
}

function formatBytes(int $bytes, int $precision = 2): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// =====================================================
// PERMISSION HELPER FUNCTIONS
// =====================================================

/**
 * Get human-readable permission string (rwx format)
 */
function getPermissionString(string $path): string {
    if (!file_exists($path)) return '---------';
    $perms = @fileperms($path);
    if ($perms === false) return '---------';
    
    $info = '';
    // Owner permissions
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
    // Group permissions
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
    // World permissions
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));
    
    return $info;
}

/**
 * Get detailed permission info array
 */
function getPermissionInfo(string $path): array {
    if (!file_exists($path)) {
        return [
            'readable' => false,
            'writable' => false,
            'executable' => false,
            'perms_octal' => '0000',
            'perms_string' => '---------',
            'owner' => 'unknown',
            'group' => 'unknown',
            'exists' => false,
        ];
    }
    
    $perms = @fileperms($path);
    $owner = 'unknown';
    $group = 'unknown';
    
    // Try to get owner/group names (Unix only)
    if (function_exists('posix_getpwuid')) {
        $ownerInfo = @posix_getpwuid(@fileowner($path));
        $owner = $ownerInfo['name'] ?? (string)@fileowner($path);
    } else {
        $owner = (string)@fileowner($path);
    }
    
    if (function_exists('posix_getgrgid')) {
        $groupInfo = @posix_getgrgid(@filegroup($path));
        $group = $groupInfo['name'] ?? (string)@filegroup($path);
    } else {
        $group = (string)@filegroup($path);
    }
    
    return [
        'readable' => is_readable($path),
        'writable' => is_writable($path),
        'executable' => is_executable($path),
        'perms_octal' => substr(sprintf('%o', $perms), -4),
        'perms_string' => getPermissionString($path),
        'owner' => $owner,
        'group' => $group,
        'exists' => true,
    ];
}

/**
 * Check if an operation is allowed based on file permissions
 * Returns array with 'allowed' boolean and 'error' message
 */
function checkOperationPermission(string $path, string $operation): array {
    $result = ['allowed' => true, 'error' => null, 'code' => null];
    
    switch ($operation) {
        case 'read':
        case 'download':
        case 'preview':
        case 'get_content':
            if (!file_exists($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'File not found.',
                    'code' => 'FILE_NOT_FOUND'
                ];
            } elseif (!is_readable($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'Permission Denied: Cannot read this file. The hosting server has restricted read access. Contact your hosting provider or use SSH/cPanel to check file permissions (should be at least 644 for files, 755 for folders).',
                    'code' => 'PERMISSION_READ_DENIED'
                ];
            }
            break;
            
        case 'write':
        case 'edit':
        case 'save':
            if (!file_exists($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'File not found.',
                    'code' => 'FILE_NOT_FOUND'
                ];
            } elseif (!is_writable($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'Permission Denied: Cannot write to this file. The hosting server has restricted write access. Use SSH/cPanel File Manager to change file permissions (chmod 644 for files, 755 for folders).',
                    'code' => 'PERMISSION_WRITE_DENIED'
                ];
            }
            break;
            
        case 'delete':
            if (!file_exists($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'File not found.',
                    'code' => 'FILE_NOT_FOUND'
                ];
            } else {
                $parent = dirname($path);
                if (!is_writable($parent)) {
                    $result = [
                        'allowed' => false,
                        'error' => 'Permission Denied: Cannot delete this item. The parent folder is read-only. Contact your hosting provider or use SSH to change directory permissions.',
                        'code' => 'PERMISSION_DELETE_DENIED'
                    ];
                }
            }
            break;
            
        case 'create':
            if (!is_dir($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'Directory not found.',
                    'code' => 'DIR_NOT_FOUND'
                ];
            } elseif (!is_writable($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'Permission Denied: Cannot create files/folders in this directory. The folder is read-only. Use SSH/cPanel to modify folder permissions (chmod 755).',
                    'code' => 'PERMISSION_CREATE_DENIED'
                ];
            }
            break;
            
        case 'rename':
        case 'move':
            if (!file_exists($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'File not found.',
                    'code' => 'FILE_NOT_FOUND'
                ];
            } else {
                $parent = dirname($path);
                if (!is_writable($parent)) {
                    $result = [
                        'allowed' => false,
                        'error' => 'Permission Denied: Cannot rename/move this item. The parent directory is read-only. Check file permissions in your hosting control panel.',
                        'code' => 'PERMISSION_MODIFY_DENIED'
                    ];
                }
            }
            break;
            
        case 'upload':
            if (!is_dir($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'Upload directory not found.',
                    'code' => 'DIR_NOT_FOUND'
                ];
            } elseif (!is_writable($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'Permission Denied: Cannot upload files to this folder. The directory is read-only. Use SSH/cPanel to change folder permissions (chmod 755 or 775).',
                    'code' => 'PERMISSION_UPLOAD_DENIED'
                ];
            }
            break;
            
        case 'extract':
        case 'compress':
            if (!file_exists($path)) {
                $result = [
                    'allowed' => false,
                    'error' => 'Source not found.',
                    'code' => 'FILE_NOT_FOUND'
                ];
            } else {
                $parent = dirname($path);
                if (!is_writable($parent)) {
                    $result = [
                        'allowed' => false,
                        'error' => 'Permission Denied: Cannot create archive/extract files. The destination directory is read-only.',
                        'code' => 'PERMISSION_ARCHIVE_DENIED'
                    ];
                }
            }
            break;
    }
    
    return $result;
}

function getFileIcon(string $filename, bool $isDir = false): string {
    if ($isDir) return '📁';
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'php' => '🐘', 'html' => '🌐', 'htm' => '🌐', 'css' => '🎨', 'scss' => '🎨', 'less' => '🎨',
        'js' => '⚡', 'ts' => '💎', 'jsx' => '⚛️', 'tsx' => '⚛️', 'vue' => '💚',
        'json' => '📋', 'xml' => '📋', 'yaml' => '📋', 'yml' => '📋',
        'txt' => '📄', 'md' => '📝', 'rtf' => '📄',
        'pdf' => '📕', 'doc' => '📘', 'docx' => '📘', 'odt' => '📘',
        'xls' => '📗', 'xlsx' => '📗', 'ods' => '📗', 'csv' => '📗',
        'ppt' => '📙', 'pptx' => '📙', 'odp' => '📙',
        'zip' => '📦', 'rar' => '📦', '7z' => '📦', 'tar' => '📦', 'gz' => '📦', 'bz2' => '📦',
        'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️', 'webp' => '🖼️', 
        'svg' => '🖼️', 'bmp' => '🖼️', 'ico' => '🖼️',
        'mp4' => '🎬', 'webm' => '🎬', 'avi' => '🎬', 'mov' => '🎬', 'mkv' => '🎬',
        'mp3' => '🎵', 'wav' => '🎵', 'ogg' => '🎵', 'flac' => '🎵', 'm4a' => '🎵',
        'sql' => '🗃️', 'db' => '🗃️', 'sqlite' => '🗃️',
        'env' => '🔐', 'key' => '🔐', 'pem' => '🔐',
        'log' => '📜', 'sh' => '⚙️', 'bat' => '⚙️', 'ps1' => '⚙️',
        'py' => '🐍', 'rb' => '💎', 'go' => '🐹', 'rs' => '🦀', 'java' => '☕', 'c' => '🔧', 'cpp' => '🔧', 'h' => '🔧',
    ];
    return $icons[$ext] ?? '📄';
}

function getAceMode(string $filename): string {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $basename = strtolower(basename($filename));
    $dotFileModes = [
        '.htaccess' => 'apache_conf',
        '.htpasswd' => 'text',
        '.env' => 'properties',
        '.env.local' => 'properties',
        '.env.development' => 'properties',
        '.env.production' => 'properties',
        '.env.example' => 'properties',
        '.gitignore' => 'gitignore',
        '.gitattributes' => 'gitignore',
        '.dockerignore' => 'gitignore',
        '.npmrc' => 'ini',
        '.yarnrc' => 'yaml',
        '.nvmrc' => 'text',
        '.editorconfig' => 'ini',
        '.prettierrc' => 'json',
        '.eslintrc' => 'json',
        '.babelrc' => 'json',
        '.stylelintrc' => 'json',
        '.browserslistrc' => 'text',
        '.bashrc' => 'sh',
        '.bash_profile' => 'sh',
        '.bash_aliases' => 'sh',
        '.zshrc' => 'sh',
        '.zprofile' => 'sh',
        '.profile' => 'sh',
        'dockerfile' => 'dockerfile',
        'makefile' => 'makefile',
        'gemfile' => 'ruby',
        'rakefile' => 'ruby',
        'vagrantfile' => 'ruby',
        'procfile' => 'text',
        'caddyfile' => 'text',
        '.htgroups' => 'text',
    ];
    if (isset($dotFileModes[$basename])) {
        return $dotFileModes[$basename];
    }
    $modes = [
        'php' => 'php', 'phtml' => 'php', 'inc' => 'php',
        'html' => 'html', 'htm' => 'html', 'xhtml' => 'html',
        'css' => 'css', 'scss' => 'scss', 'sass' => 'sass', 'less' => 'less',
        'js' => 'javascript', 'mjs' => 'javascript', 'cjs' => 'javascript',
        'ts' => 'typescript', 'tsx' => 'tsx', 'jsx' => 'jsx', 'vue' => 'html',
        'json' => 'json', 'json5' => 'json5', 'jsonc' => 'json',
        'xml' => 'xml', 'xsl' => 'xml', 'xslt' => 'xml', 'svg' => 'xml',
        'yaml' => 'yaml', 'yml' => 'yaml',
        'md' => 'markdown', 'markdown' => 'markdown', 'mdown' => 'markdown',
        'txt' => 'text', 'text' => 'text', 'log' => 'text', 'nfo' => 'text',
        'sql' => 'sql', 'mysql' => 'mysql', 'pgsql' => 'pgsql',
        'sh' => 'sh', 'bash' => 'sh', 'zsh' => 'sh', 'fish' => 'sh', 'ksh' => 'sh',
        'py' => 'python', 'pyw' => 'python', 'pyx' => 'python',
        'rb' => 'ruby', 'erb' => 'ruby', 'rake' => 'ruby',
        'go' => 'golang',
        'rs' => 'rust',
        'java' => 'java', 'class' => 'java', 'jar' => 'java',
        'c' => 'c_cpp', 'cpp' => 'c_cpp', 'cc' => 'c_cpp', 'cxx' => 'c_cpp', 
        'h' => 'c_cpp', 'hpp' => 'c_cpp', 'hh' => 'c_cpp',
        'cs' => 'csharp',
        'swift' => 'swift',
        'kt' => 'kotlin', 'kts' => 'kotlin',
        'scala' => 'scala',
        'lua' => 'lua',
        'pl' => 'perl', 'pm' => 'perl', 'perl' => 'perl',
        'r' => 'r', 'rmd' => 'r',
        'dockerfile' => 'dockerfile',
        'makefile' => 'makefile', 'mk' => 'makefile',
        'ini' => 'ini', 'cfg' => 'ini', 'conf' => 'ini', 'properties' => 'properties',
        'htaccess' => 'apache_conf', 'htpasswd' => 'text', 'htgroups' => 'text',
        'nginx' => 'nginx',
        'env' => 'properties',
        'gitignore' => 'gitignore', 'gitattributes' => 'gitignore',
        'toml' => 'toml',
        'graphql' => 'graphqlschema', 'gql' => 'graphqlschema',
        'proto' => 'protobuf',
        'diff' => 'diff', 'patch' => 'diff',
        'csv' => 'text', 'tsv' => 'text',
    ];
    return $modes[$ext] ?? 'text';
}

function getMimeType(string $filename): string {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mimes = [
        'txt' => 'text/plain', 'html' => 'text/html', 'htm' => 'text/html', 'css' => 'text/css',
        'js' => 'application/javascript', 'json' => 'application/json', 'xml' => 'application/xml',
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
        'webp' => 'image/webp', 'svg' => 'image/svg+xml', 'ico' => 'image/x-icon',
        'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg',
        'mp3' => 'audio/mpeg', 'wav' => 'audio/wav',
        'pdf' => 'application/pdf', 'zip' => 'application/zip',
        'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint', 'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];
    return $mimes[$ext] ?? 'application/octet-stream';
}

function isImage(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico']);
}

function isVideo(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv']);
}

function isAudio(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['mp3', 'wav', 'ogg', 'flac', 'm4a']);
}

function isDocument(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp']);
}

function isArchive(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['zip', 'tar', 'gz', 'bz2', '7z', 'rar']);
}

function isEditable(string $filename): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $binaryExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico', 'svg', 'mp4', 'webm', 'avi', 'mov', 'mkv',
                   'mp3', 'wav', 'flac', 'ogg', 'm4a', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
                   'zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'exe', 'dll', 'so', 'bin', 'dat', 'db', 'sqlite'];
    return !in_array($ext, $binaryExts);
}

function getSharesData(): array {
    global $CONFIG;
    $file = $CONFIG['share_data_file'];
    if (!file_exists($file)) {
        return [];
    }
    $data = @file_get_contents($file);
    return $data ? json_decode($data, true) ?? [] : [];
}

function saveSharesData(array $shares): bool {
    global $CONFIG;
    return file_put_contents($CONFIG['share_data_file'], json_encode($shares, JSON_PRETTY_PRINT)) !== false;
}

function createShareLink(string $filePath, string $expiry = '24h'): ?array {
    global $CONFIG;
    if (!file_exists($filePath) || !is_file($filePath)) {
        return null;
    }
    $token = bin2hex(random_bytes(32));
    $createdAt = time();
    $expiresAt = null;
    switch ($expiry) {
        case '1h': $expiresAt = $createdAt + 3600; break;
        case '24h': $expiresAt = $createdAt + 86400; break;
        case '7d': $expiresAt = $createdAt + 604800; break;
        case '30d': $expiresAt = $createdAt + 2592000; break;
        case 'never': $expiresAt = null; break;
        default: $expiresAt = $createdAt + 86400;
    }
    $shares = getSharesData();
    $shares[$token] = [
        'token' => $token,
        'file_path' => $filePath,
        'file_name' => basename($filePath),
        'created_at' => $createdAt,
        'expires_at' => $expiresAt,
        'expiry_type' => $expiry,
        'downloads' => 0,
        'created_by' => $_SESSION['username'] ?? 'unknown',
    ];
    if (saveSharesData($shares)) {
        return $shares[$token];
    }
    return null;
}

function getShareByToken(string $token): ?array {
    $shares = getSharesData();
    return $shares[$token] ?? null;
}

function isShareValid(array $share): bool {
    if (!file_exists($share['file_path']) || !is_file($share['file_path'])) {
        return false;
    }
    if ($share['expires_at'] !== null && time() > $share['expires_at']) {
        return false;
    }
    return true;
}

function deleteShare(string $token): bool {
    $shares = getSharesData();
    if (isset($shares[$token])) {
        unset($shares[$token]);
        return saveSharesData($shares);
    }
    return false;
}

function incrementShareDownloads(string $token): void {
    $shares = getSharesData();
    if (isset($shares[$token])) {
        $shares[$token]['downloads']++;
        saveSharesData($shares);
    }
}

function cleanupExpiredShares(): int {
    $shares = getSharesData();
    $count = 0;
    $now = time();
    foreach ($shares as $token => $share) {
        if ($share['expires_at'] !== null && $now > $share['expires_at']) {
            unset($shares[$token]);
            $count++;
        }
    }
    if ($count > 0) {
        saveSharesData($shares);
    }
    return $count;
}

function getDirectUrl(string $relativePath, string $filename): string {
    global $CONFIG;
    $path = $relativePath ? $relativePath . '/' . $filename : $filename;
    return $CONFIG['base_url'] . '?path=' . urlencode(dirname($path) === '.' ? '' : dirname($path)) . 
           '&preview=' . urlencode($filename);
}

function searchFiles(string $directory, string $query, string $basePath = ''): array {
    global $CONFIG;
    $results = [];
    $query = strtolower($query);
    if (!is_dir($directory)) return $results;
    $items = @scandir($directory);
    if ($items === false) return $results;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        if (shouldExclude($item, is_dir($directory . '/' . $item))) continue;
        $path = $directory . '/' . $item;
        $relativePath = $basePath ? $basePath . '/' . $item : $item;
        if (stripos($item, $query) !== false) {
            $results[] = [
                'name' => $item,
                'path' => $relativePath,
                'is_dir' => is_dir($path),
                'size' => is_file($path) ? filesize($path) : 0,
                'modified' => filemtime($path),
            ];
        }
        if (is_dir($path) && count($results) < 100) {
            $results = array_merge($results, searchFiles($path, $query, $relativePath));
        }
    }
    return array_slice($results, 0, 100);
}

function deleteRecursive(string $path): bool {
    if (is_file($path)) {
        return unlink($path);
    }
    if (!is_dir($path)) {
        return false;
    }
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        deleteRecursive($path . '/' . $item);
    }
    return rmdir($path);
}

function copyRecursive(string $source, string $dest): bool {
    if (is_file($source)) {
        return copy($source, $dest);
    }
    if (!is_dir($dest)) {
        mkdir($dest, 0755, true);
    }
    $items = scandir($source);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        copyRecursive($source . '/' . $item, $dest . '/' . $item);
    }
    return true;
}

/**
 * Count total items in a directory recursively
 */
function countItemsRecursive(string $path): int {
    $count = 1; // Count the path itself
    if (is_dir($path)) {
        $items = @scandir($path);
        if ($items !== false) {
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $count += countItemsRecursive($path . '/' . $item);
            }
        }
    }
    return $count;
}

/**
 * Recursively change permissions on a directory and all its contents
 * @param string $path The path to chmod
 * @param int $dirMode The octal mode for directories
 * @param int $fileMode The octal mode for files
 * @return array ['success' => int, 'failed' => int, 'errors' => array]
 */
function chmodRecursive(string $path, int $dirMode, int $fileMode): array {
    $result = ['success' => 0, 'failed' => 0, 'errors' => []];
    
    if (!file_exists($path)) {
        $result['failed']++;
        $result['errors'][] = "Path does not exist: " . basename($path);
        return $result;
    }
    
    // Apply chmod to the current item
    $isDir = is_dir($path);
    $mode = $isDir ? $dirMode : $fileMode;
    $chmodResult = @chmod($path, $mode);
    
    if ($chmodResult) {
        clearstatcache(true, $path);
        $result['success']++;
    } else {
        $result['failed']++;
        $result['errors'][] = basename($path) . " - permission denied";
    }
    
    // If it's a directory, recurse into it
    if ($isDir) {
        $items = @scandir($path);
        if ($items !== false) {
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $childResult = chmodRecursive($path . '/' . $item, $dirMode, $fileMode);
                $result['success'] += $childResult['success'];
                $result['failed'] += $childResult['failed'];
                $result['errors'] = array_merge($result['errors'], $childResult['errors']);
            }
        }
    }
    
    return $result;
}

/**
 * Recursively change permissions with streaming progress updates (SSE)
 * @param string $path The path to chmod
 * @param int $dirMode The octal mode for directories
 * @param int $fileMode The octal mode for files
 * @param int $total Total items to process
 * @param int &$processed Reference to processed count
 * @param array &$result Reference to result array
 */
function chmodRecursiveWithProgress(string $path, int $dirMode, int $fileMode, int $total, int &$processed, array &$result): void {
    if (!file_exists($path)) {
        $result['failed']++;
        $result['errors'][] = "Path does not exist: " . basename($path);
        $processed++;
        return;
    }
    
    // Apply chmod to the current item
    $isDir = is_dir($path);
    $mode = $isDir ? $dirMode : $fileMode;
    $chmodResult = @chmod($path, $mode);
    
    if ($chmodResult) {
        clearstatcache(true, $path);
        $result['success']++;
    } else {
        $result['failed']++;
        if (count($result['errors']) < 10) {
            $result['errors'][] = basename($path) . " - permission denied";
        }
    }
    
    $processed++;
    
    // Send progress update every 5 items or at the end
    if ($processed % 5 === 0 || $processed === $total) {
        $percent = min(100, round(($processed / $total) * 100));
        echo "data: " . json_encode([
            'type' => 'progress',
            'processed' => $processed,
            'total' => $total,
            'percent' => $percent,
            'success' => $result['success'],
            'failed' => $result['failed'],
            'current' => basename($path)
        ]) . "\n\n";
        @ob_flush();
        @flush();
    }
    
    // If it's a directory, recurse into it
    if ($isDir) {
        $items = @scandir($path);
        if ($items !== false) {
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                chmodRecursiveWithProgress($path . '/' . $item, $dirMode, $fileMode, $total, $processed, $result);
            }
        }
    }
}

function createBackup(string $path, string $backupDir): ?string {
    $name = basename($path);
    $timestamp = date('Y-m-d_H-i-s');
    $backupName = $name . '_backup_' . $timestamp;
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    $backupPath = $backupDir . '/' . $backupName;
    if (is_file($path)) {
        if (copy($path, $backupPath)) {
            return $backupPath;
        }
    } else if (is_dir($path)) {
        if (copyRecursive($path, $backupPath)) {
            return $backupPath;
        }
    }
    return null;
}

function createZip(string $source, string $destination): bool {
    if (!class_exists('ZipArchive')) {
        return false;
    }
    $zip = new ZipArchive();
    if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }
    $source = realpath($source);
    if (is_file($source)) {
        $zip->addFile($source, basename($source));
    } else {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);
            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
    return $zip->close();
}

function extractZip(string $zipFile, string $destination): bool {
    if (!class_exists('ZipArchive')) {
        return false;
    }
    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== true) {
        return false;
    }
    $result = $zip->extractTo($destination);
    $zip->close();
    return $result;
}

function createTar(string $source, string $destination, bool $gzip = true): bool {
    if (!class_exists('PharData')) {
        return false;
    }
    try {
        $tarName = $gzip ? preg_replace('/\.gz$/', '', $destination) : $destination;
        $phar = new PharData($tarName);
        $source = realpath($source);
        if (is_file($source)) {
            $phar->addFile($source, basename($source));
        } else {
            $phar->buildFromDirectory($source);
        }
        if ($gzip) {
            $phar->compress(Phar::GZ);
            unlink($tarName);
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function extractTar(string $tarFile, string $destination): bool {
    if (!class_exists('PharData')) {
        return false;
    }
    try {
        $phar = new PharData($tarFile);
        $phar->extractTo($destination);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getMaliciousPatterns(): array {
    return [
        'eval_usage' => [
            'pattern' => '/\beval\s*\(/i',
            'severity' => 'critical',
            'description' => 'eval() function - can execute arbitrary code',
            'category' => 'Code Execution'
        ],
        'base64_exec' => [
            'pattern' => '/base64_decode\s*\([^)]*\)\s*[;)]*\s*(?:\)|;)?\s*(?:eval|exec|system|passthru|shell_exec)/i',
            'severity' => 'critical',
            'description' => 'Base64 encoded code execution',
            'category' => 'Obfuscation'
        ],
        'shell_exec' => [
            'pattern' => '/\b(shell_exec|exec|system|passthru|popen|proc_open)\s*\(/i',
            'severity' => 'high',
            'description' => 'Shell command execution function',
            'category' => 'Code Execution'
        ],
        'file_include' => [
            'pattern' => '/\b(include|require|include_once|require_once)\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            'severity' => 'critical',
            'description' => 'Remote/Local File Inclusion vulnerability',
            'category' => 'File Inclusion'
        ],
        'preg_replace_e' => [
            'pattern' => '/preg_replace\s*\(\s*["\'][^"\']*\/[a-z]*e[a-z]*["\']/',
            'severity' => 'critical',
            'description' => 'preg_replace with /e modifier (code execution)',
            'category' => 'Code Execution'
        ],
        'assert_function' => [
            'pattern' => '/\bassert\s*\(\s*\$_(GET|POST|REQUEST)/i',
            'severity' => 'critical',
            'description' => 'assert() with user input',
            'category' => 'Code Execution'
        ],
        'create_function' => [
            'pattern' => '/\bcreate_function\s*\(/i',
            'severity' => 'high',
            'description' => 'create_function() - deprecated and dangerous',
            'category' => 'Code Execution'
        ],
        'backtick_exec' => [
            'pattern' => '/`[^`]*\$_(GET|POST|REQUEST|COOKIE)[^`]*`/i',
            'severity' => 'critical',
            'description' => 'Backtick shell execution with user input',
            'category' => 'Code Execution'
        ],
        'sql_injection' => [
            'pattern' => '/\$_(GET|POST|REQUEST)\s*\[[^\]]+\]\s*[^;]*\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION)\b/i',
            'severity' => 'high',
            'description' => 'Possible SQL Injection vulnerability',
            'category' => 'SQL Injection'
        ],
        'xss_echo' => [
            'pattern' => '/echo\s+\$_(GET|POST|REQUEST|COOKIE)\s*\[/i',
            'severity' => 'medium',
            'description' => 'Direct output of user input (XSS risk)',
            'category' => 'XSS'
        ],
        'file_write_input' => [
            'pattern' => '/file_put_contents\s*\(\s*\$_(GET|POST|REQUEST)/i',
            'severity' => 'critical',
            'description' => 'File write with user-controlled path',
            'category' => 'File Operation'
        ],
        'unlink_input' => [
            'pattern' => '/\bunlink\s*\(\s*\$_(GET|POST|REQUEST)/i',
            'severity' => 'high',
            'description' => 'File deletion with user-controlled path',
            'category' => 'File Operation'
        ],
        'base64_decode' => [
            'pattern' => '/base64_decode\s*\(\s*["\'][A-Za-z0-9+\/=]{50,}["\']\s*\)/i',
            'severity' => 'medium',
            'description' => 'Long base64 encoded string (possible obfuscation)',
            'category' => 'Obfuscation'
        ],
        'gzinflate' => [
            'pattern' => '/gzinflate\s*\(\s*base64_decode/i',
            'severity' => 'high',
            'description' => 'Compressed and encoded payload',
            'category' => 'Obfuscation'
        ],
        'hex_decode' => [
            'pattern' => '/\\\\x[0-9a-fA-F]{2}(\\\\x[0-9a-fA-F]{2}){10,}/i',
            'severity' => 'medium',
            'description' => 'Hex-encoded string (possible obfuscation)',
            'category' => 'Obfuscation'
        ],
        'webshell_patterns' => [
            'pattern' => '/\b(c99|r57|b374k|wso|alfa|webshell|backdoor)\b/i',
            'severity' => 'critical',
            'description' => 'Known webshell signature detected',
            'category' => 'Webshell'
        ],
        'hidden_iframe' => [
            'pattern' => '/<iframe[^>]*(?:style\s*=\s*["\'][^"\']*(?:display\s*:\s*none|visibility\s*:\s*hidden|width\s*:\s*0|height\s*:\s*0)[^"\']*["\']|width\s*=\s*["\']?0|height\s*=\s*["\']?0)/i',
            'severity' => 'high',
            'description' => 'Hidden iframe detected',
            'category' => 'Malware'
        ],
        'crypto_miner' => [
            'pattern' => '/\b(coinhive|cryptonight|monero|coin-hive|minero|jsecoin)\b/i',
            'severity' => 'high',
            'description' => 'Cryptocurrency miner detected',
            'category' => 'Malware'
        ],
        'reverse_shell' => [
            'pattern' => '/fsockopen\s*\([^)]*\$_(GET|POST|REQUEST)/i',
            'severity' => 'critical',
            'description' => 'Possible reverse shell connection',
            'category' => 'Backdoor'
        ],
        'disable_functions' => [
            'pattern' => '/ini_set\s*\(\s*["\']disable_functions["\']/i',
            'severity' => 'high',
            'description' => 'Attempting to modify disable_functions',
            'category' => 'Security Bypass'
        ],
    ];
}

function scanFileForMalware(string $filePath): array {
    $result = [
        'file' => basename($filePath),
        'path' => $filePath,
        'scanned' => true,
        'is_clean' => true,
        'findings' => [],
        'total_issues' => 0,
    ];
    if (!file_exists($filePath) || !is_file($filePath)) {
        $result['error'] = 'File not found';
        $result['scanned'] = false;
        return $result;
    }
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $scannableExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'phar', 'inc',
                      'html', 'htm', 'js', 'jsx', 'ts', 'tsx', 'asp', 'aspx', 'jsp', 'cgi', 'pl'];
    if (!in_array($ext, $scannableExts)) {
        $result['skipped'] = true;
        $result['skip_reason'] = 'File type not scannable';
        return $result;
    }
    $maxSize = 2 * 1024 * 1024;
    if (filesize($filePath) > $maxSize) {
        $result['skipped'] = true;
        $result['skip_reason'] = 'File too large to scan';
        return $result;
    }
    $content = @file_get_contents($filePath);
    if ($content === false) {
        $result['error'] = 'Could not read file';
        $result['scanned'] = false;
        return $result;
    }
    $patterns = getMaliciousPatterns();
    $lines = explode("\n", $content);
    foreach ($patterns as $name => $patternInfo) {
        if (preg_match_all($patternInfo['pattern'], $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $position = $match[1];
                $lineNumber = substr_count(substr($content, 0, $position), "\n") + 1;
                $lineContent = isset($lines[$lineNumber - 1]) ? trim($lines[$lineNumber - 1]) : '';
                if (strlen($lineContent) > 100) {
                    $lineContent = substr($lineContent, 0, 100) . '...';
                }
                $result['findings'][] = [
                    'pattern' => $name,
                    'severity' => $patternInfo['severity'],
                    'description' => $patternInfo['description'],
                    'category' => $patternInfo['category'],
                    'line' => $lineNumber,
                    'line_content' => $lineContent,
                ];
                $result['is_clean'] = false;
            }
        }
    }
    $result['total_issues'] = count($result['findings']);
    return $result;
}

function scanDirectoryForMalware(string $directory, int $maxFiles = 500): array {
    $results = [
        'directory' => $directory,
        'scanned' => 0,
        'infected' => 0,
        'skipped' => 0,
        'critical' => 0,
        'high' => 0,
        'medium' => 0,
        'results' => [],
        'errors' => [],
    ];
    if (!is_dir($directory)) {
        $results['errors'][] = 'Directory not found';
        return $results;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $fileCount = 0;
    foreach ($iterator as $file) {
        if ($fileCount >= $maxFiles) break;
        if (!$file->isFile()) continue;
        $fileCount++;
        $results['scanned']++;
        $scanResult = scanFileForMalware($file->getRealPath());
        if (isset($scanResult['skipped']) && $scanResult['skipped']) {
            $results['skipped']++;
            continue;
        }
        if (!$scanResult['is_clean']) {
            $results['infected']++;
            $relativePath = str_replace($directory . '/', '', $file->getRealPath());
            $scanResult['file'] = $relativePath;
            $results['results'][] = $scanResult;
            foreach ($scanResult['findings'] as $finding) {
                switch ($finding['severity']) {
                    case 'critical': $results['critical']++; break;
                    case 'high': $results['high']++; break;
                    case 'medium': $results['medium']++; break;
                }
            }
        }
    }
    return $results;
}

if (isset($_GET['share']) && $CONFIG['share_enabled']) {
    $shareToken = $_GET['share'];
    $share = getShareByToken($shareToken);
    if (!$share) {
        http_response_code(404);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Share Not Found</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <style>body { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); }</style>
        </head>
        <body class="min-h-screen flex items-center justify-center p-4 text-white">
            <div class="text-center">
                <div class="text-8xl mb-6">🔗</div>
                <h1 class="text-3xl font-bold mb-2">Link Not Found</h1>
                <p class="text-slate-400">This share link doesn't exist or has been removed.</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    if (!isShareValid($share)) {
        http_response_code(410);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Link Expired</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <style>body { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); }</style>
        </head>
        <body class="min-h-screen flex items-center justify-center p-4 text-white">
            <div class="text-center">
                <div class="text-8xl mb-6">⏰</div>
                <h1 class="text-3xl font-bold mb-2">Link Expired</h1>
                <p class="text-slate-400">This share link has expired or the file no longer exists.</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    if (isset($_GET['download'])) {
        incrementShareDownloads($shareToken);
        $filePath = $share['file_path'];
        $fileName = $share['file_name'];
        $mimeType = getMimeType($fileName);
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache');
        readfile($filePath);
        exit;
    }
    $filePath = $share['file_path'];
    $fileName = $share['file_name'];
    $fileSize = formatBytes(filesize($filePath));
    $fileIcon = getFileIcon($fileName);
    $expiresText = $share['expires_at'] ? date('M j, Y g:i A', $share['expires_at']) : 'Never';
    $downloadUrl = '?share=' . $shareToken . '&download=1';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Download: <?= htmlspecialchars($fileName) ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); }
            .glass { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center p-4 text-white">
        <div class="glass rounded-3xl shadow-2xl p-8 w-full max-w-md border border-slate-700/50 text-center">
            <div class="text-6xl mb-4"><?= $fileIcon ?></div>
            <h1 class="text-xl font-bold mb-2 break-all"><?= htmlspecialchars($fileName) ?></h1>
            <p class="text-slate-400 text-sm mb-6"><?= $fileSize ?></p>
            <a href="<?= $downloadUrl ?>" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                <span>⬇️</span> Download File
            </a>
            <div class="mt-6 pt-6 border-t border-slate-700/50 text-xs text-slate-500">
                <p>Expires: <?= $expiresText ?></p>
                <p class="mt-1">Downloads: <?= $share['downloads'] ?></p>
            </div>
            <p class="text-xs text-slate-600 mt-6">
                Shared via File Manager Pro<br>
                <a href="https://t.me/anusoni1024" target="_blank" rel="noopener noreferrer" 
                   class="text-cyan-500 hover:text-cyan-400 transition-colors">
                    Created by @anusoni1024 💬
                </a>
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (!checkIPAccess($CONFIG)) {
    http_response_code(403);
    die('Access denied');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Check if IP is locked out
        if (isIPLocked($clientIP)) {
            $remainingTime = getRemainingLockoutTime($clientIP);
            $error = 'Too many failed attempts. Try again in ' . ceil($remainingTime / 60) . ' minutes.';
            auditLog('LOGIN_BLOCKED', "Locked IP attempted login", 'WARN');
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Input validation
            if (strlen($username) > 64 || strlen($password) > 128) {
                $error = 'Invalid input length';
                auditLog('LOGIN_INVALID', "Input validation failed", 'WARN');
            } else {
                $allUsers = getAllUsers();
                if (isset($allUsers[$username]) && 
                    password_verify($password, $allUsers[$username]['password'])) {
                    
                    // Successful login
                    recordLoginAttempt($clientIP, true);
                    session_regenerate_id(true);
                    
                    $_SESSION['authenticated'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['last_activity'] = time();
                    $_SESSION['created_at'] = time();
                    $_SESSION['fingerprint'] = generateSessionFingerprint();
                    $_SESSION['login_ip'] = $clientIP;
                    
                    auditLog('LOGIN_SUCCESS', "User logged in successfully");
                    
                    // Check if password change required
                    if (!empty($allUsers[$username]['must_change_password'])) {
                        $_SESSION['must_change_password'] = true;
                    }
                    
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    // Failed login
                    recordLoginAttempt($clientIP, false);
                    $error = 'Invalid username or password';
                    auditLog('LOGIN_FAILED', "Failed login for user: " . htmlspecialchars($username), 'WARN');
                    
                    // Show remaining attempts
                    $attempts = getLoginAttempts();
                    if (isset($attempts[$clientIP])) {
                        $remaining = FM_MAX_LOGIN_ATTEMPTS - $attempts[$clientIP]['count'];
                        if ($remaining > 0 && $remaining < FM_MAX_LOGIN_ATTEMPTS) {
                            $error .= " ($remaining attempts remaining)";
                        }
                    }
                }
            }
        }
    }
    if ($_POST['action'] === 'logout') {
        auditLog('LOGOUT', "User logged out");
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (!isLoggedIn()):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>File Manager - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); }
        .glass { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="glass rounded-3xl shadow-2xl p-8 w-full max-w-md border border-slate-700/50">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">📂</div>
            <h1 class="text-2xl font-bold text-white">File Manager Pro</h1>
            <p class="text-slate-400 mt-2">Sign in to continue</p>
        </div>
        <?php if ($error): ?>
        <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl mb-4 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="login">
            <div>
                <input type="text" name="username" placeholder="Username" required autocomplete="username"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required autocomplete="current-password"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
            </div>
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                Sign In
            </button>
        </form>
        <p class="text-center text-slate-500 text-xs mt-6">
            File Manager Pro v3.0<br>
            <a href="https://t.me/anusoni1024" target="_blank" rel="noopener noreferrer" 
               class="text-cyan-500 hover:text-cyan-400 transition-colors">
                Created by @anusoni1024 💬
            </a>
        </p>
    </div>
</body>
</html>
<?php
exit;
endif;

$currentPath = $_GET['path'] ?? '';
$currentPath = sanitizePath($currentPath);
$fullPath = getFullPath($currentPath);
$userRoot = getUserRoot();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] !== 'login' && $_POST['action'] !== 'logout') {
    header('Content-Type: application/json');
    if (!verifyCSRF()) {
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }
    switch ($_POST['action']) {
        case 'upload':
            // Check folder write permission first
            $permCheck = checkOperationPermission($fullPath, 'upload');
            if (!$permCheck['allowed']) {
                echo json_encode(['success' => false, 'error' => $permCheck['error']]);
                exit;
            }
            
            if (!empty($_FILES['files'])) {
                $uploaded = [];
                $errors = [];
                $files = $_FILES['files'];
                
                // Dangerous MIME types to block
                $dangerousMimes = [
                    'application/x-php', 'application/php', 'application/x-httpd-php',
                    'text/php', 'text/x-php', 'application/x-httpd-php-source',
                    'application/x-executable', 'application/x-msdos-program',
                    'application/x-shellscript', 'application/x-sh'
                ];
                
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $filename = basename($files['name'][$i]);
                        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                        
                        // Extension check
                        if (!isExtensionAllowed($filename)) {
                            $errors[] = "$filename: Extension not allowed";
                            auditLog('UPLOAD_BLOCKED', "Blocked extension: $filename", 'WARN');
                            continue;
                        }
                        
                        // Size check
                        if ($files['size'][$i] > $CONFIG['max_upload_size']) {
                            $errors[] = "$filename: File too large";
                            continue;
                        }
                        
                        // MIME type verification (for non-admin users)
                        if (!isAdmin()) {
                            $finfo = new finfo(FILEINFO_MIME_TYPE);
                            $detectedMime = $finfo->file($files['tmp_name'][$i]);
                            
                            if (in_array($detectedMime, $dangerousMimes)) {
                                $errors[] = "$filename: Dangerous file type detected";
                                auditLog('UPLOAD_BLOCKED', "Blocked MIME type: $filename ($detectedMime)", 'WARN');
                                continue;
                            }
                            
                            // Check for PHP code in files that claim to be images
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                $content = file_get_contents($files['tmp_name'][$i], false, null, 0, 1024);
                                if (preg_match('/<\?php|<\?=/i', $content)) {
                                    $errors[] = "$filename: Suspicious content detected";
                                    auditLog('UPLOAD_BLOCKED', "PHP code in image: $filename", 'CRITICAL');
                                    continue;
                                }
                            }
                        }
                        
                        $destination = $fullPath . '/' . $filename;
                        if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                            $uploaded[] = $filename;
                            auditLog('FILE_UPLOAD', "Uploaded: $filename to $currentPath");
                        } else {
                            $errors[] = "$filename: Upload failed";
                        }
                    }
                }
                echo json_encode([
                    'success' => count($uploaded) > 0,
                    'files' => $uploaded,
                    'errors' => $errors
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No files uploaded']);
            }
            exit;
        case 'upload_url':
            $url = filter_var($_POST['url'] ?? '', FILTER_VALIDATE_URL);
            if (!$url) {
                echo json_encode(['success' => false, 'error' => 'Invalid URL']);
                exit;
            }
            $filename = basename(parse_url($url, PHP_URL_PATH)) ?: 'downloaded_file';
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            if (!isExtensionAllowed($filename)) {
                echo json_encode(['success' => false, 'error' => 'Extension not allowed']);
                exit;
            }
            $destination = $fullPath . '/' . $filename;
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'FileManager/3.0',
                ]
            ]);
            $content = @file_get_contents($url, false, $context);
            if ($content !== false && file_put_contents($destination, $content) !== false) {
                echo json_encode(['success' => true, 'filename' => $filename]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to download file']);
            }
            exit;
        case 'create_folder':
            $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $_POST['name'] ?? '');
            if ($name) {
                $newPath = $fullPath . '/' . $name;
                if (!file_exists($newPath)) {
                    if (mkdir($newPath, 0755, true)) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to create folder']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Folder already exists']);
                }
            }
            exit;
        case 'create_file':
            $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $_POST['name'] ?? '');
            if ($name) {
                if (!isExtensionAllowed($name)) {
                    echo json_encode(['success' => false, 'error' => 'Extension not allowed']);
                    exit;
                }
                $newPath = $fullPath . '/' . $name;
                if (!file_exists($newPath)) {
                    if (file_put_contents($newPath, '') !== false) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to create file']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'File already exists']);
                }
            }
            exit;
        case 'rename':
            $oldName = basename($_POST['old_name'] ?? '');
            $newName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $_POST['new_name'] ?? '');
            if ($oldName && $newName) {
                $oldPath = $fullPath . '/' . $oldName;
                $newPath = $fullPath . '/' . $newName;
                if (file_exists($oldPath) && !file_exists($newPath)) {
                    if (rename($oldPath, $newPath)) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to rename']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid operation']);
                }
            }
            exit;
        case 'copy':
            $name = basename($_POST['name'] ?? '');
            $destination = sanitizePath($_POST['destination'] ?? '');
            if ($name) {
                $sourcePath = $fullPath . '/' . $name;
                $destPath = getFullPath($destination) . '/' . $name;
                if (file_exists($sourcePath) && !file_exists($destPath)) {
                    if (copyRecursive($sourcePath, $destPath)) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to copy']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid operation']);
                }
            }
            exit;
        case 'move':
            $name = basename($_POST['name'] ?? '');
            $destination = sanitizePath($_POST['destination'] ?? '');
            if ($name) {
                $sourcePath = $fullPath . '/' . $name;
                $destPath = getFullPath($destination) . '/' . $name;
                if (file_exists($sourcePath) && !file_exists($destPath)) {
                    if (rename($sourcePath, $destPath)) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to move']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid operation']);
                }
            }
            exit;
        case 'delete':
            $name = basename($_POST['name'] ?? '');
            if ($name) {
                $targetPath = $fullPath . '/' . $name;
                
                // Check delete permission
                $permCheck = checkOperationPermission($targetPath, 'delete');
                if (!$permCheck['allowed']) {
                    echo json_encode(['success' => false, 'error' => $permCheck['error']]);
                    exit;
                }
                
                if (file_exists($targetPath)) {
                    if (deleteRecursive($targetPath)) {
                        auditLog('FILE_DELETE', "Deleted: $name from $currentPath");
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to delete. The hosting server may have restricted access to this file.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'File not found']);
                }
            }
            exit;
        case 'bulk_delete':
            $items = json_decode($_POST['items'] ?? '[]', true);
            $deleted = [];
            $errors = [];
            foreach ($items as $name) {
                $name = basename($name);
                $targetPath = $fullPath . '/' . $name;
                if (file_exists($targetPath)) {
                    if (deleteRecursive($targetPath)) {
                        $deleted[] = $name;
                        auditLog('FILE_DELETE', "Bulk deleted: $name from $currentPath");
                    } else {
                        $errors[] = "$name: Delete failed";
                    }
                }
            }
            echo json_encode(['success' => count($deleted) > 0, 'deleted' => $deleted, 'errors' => $errors]);
            exit;
        
        case 'undo_operation':
            $operation = json_decode($_POST['operation'] ?? '{}', true);
            if (empty($operation) || !isset($operation['type'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid undo operation']);
                exit;
            }
            
            $result = ['success' => false, 'error' => 'Unknown operation type'];
            
            switch ($operation['type']) {
                case 'rename':
                    // Undo rename: rename back to original name
                    $opPath = getFullPath($operation['path'] ?? '');
                    $currentName = basename($operation['newName']);
                    $originalName = basename($operation['oldName']);
                    $currentPath_undo = $opPath . '/' . $currentName;
                    $originalPath = $opPath . '/' . $originalName;
                    
                    if (file_exists($currentPath_undo) && !file_exists($originalPath)) {
                        if (rename($currentPath_undo, $originalPath)) {
                            auditLog('UNDO_RENAME', "Reverted rename: $currentName -> $originalName");
                            $result = ['success' => true, 'message' => "Renamed '$currentName' back to '$originalName'"];
                        } else {
                            $result = ['success' => false, 'error' => 'Failed to undo rename'];
                        }
                    } else {
                        $result = ['success' => false, 'error' => 'Cannot undo: file state has changed'];
                    }
                    break;
                    
                case 'move':
                    // Undo move: move back to original location
                    $destPath = getFullPath($operation['destination'] ?? '');
                    $srcPath = getFullPath($operation['source'] ?? '');
                    $name = basename($operation['name']);
                    $currentLoc = $destPath . '/' . $name;
                    $originalLoc = $srcPath . '/' . $name;
                    
                    if (file_exists($currentLoc) && !file_exists($originalLoc)) {
                        if (rename($currentLoc, $originalLoc)) {
                            auditLog('UNDO_MOVE', "Reverted move: $name back to original location");
                            $result = ['success' => true, 'message' => "Moved '$name' back to original location"];
                        } else {
                            $result = ['success' => false, 'error' => 'Failed to undo move'];
                        }
                    } else {
                        $result = ['success' => false, 'error' => 'Cannot undo: file state has changed'];
                    }
                    break;
                    
                case 'delete':
                case 'bulk_delete':
                    // Delete cannot be undone - files are permanently removed
                    $result = ['success' => false, 'error' => 'Delete operations cannot be undone. Files have been permanently removed.'];
                    break;
                    
                default:
                    $result = ['success' => false, 'error' => 'This operation type cannot be undone'];
            }
            
            echo json_encode($result);
            exit;
        case 'bulk_move':
            $items = json_decode($_POST['items'] ?? '[]', true);
            $destination = sanitizePath($_POST['destination'] ?? '');
            $moved = [];
            $errors = [];
            foreach ($items as $name) {
                $name = basename($name);
                $sourcePath = $fullPath . '/' . $name;
                $destPath = getFullPath($destination) . '/' . $name;
                if (file_exists($sourcePath) && !file_exists($destPath)) {
                    if (rename($sourcePath, $destPath)) {
                        $moved[] = $name;
                    } else {
                        $errors[] = "$name: Move failed";
                    }
                } else {
                    $errors[] = "$name: Invalid operation";
                }
            }
            echo json_encode(['success' => count($moved) > 0, 'moved' => $moved, 'errors' => $errors]);
            exit;
        case 'bulk_copy':
            $items = json_decode($_POST['items'] ?? '[]', true);
            $destination = sanitizePath($_POST['destination'] ?? '');
            $copied = [];
            $errors = [];
            foreach ($items as $name) {
                $name = basename($name);
                $sourcePath = $fullPath . '/' . $name;
                $destPath = getFullPath($destination) . '/' . $name;
                if (file_exists($sourcePath) && !file_exists($destPath)) {
                    if (copyRecursive($sourcePath, $destPath)) {
                        $copied[] = $name;
                    } else {
                        $errors[] = "$name: Copy failed";
                    }
                } else {
                    $errors[] = "$name: Invalid operation";
                }
            }
            echo json_encode(['success' => count($copied) > 0, 'copied' => $copied, 'errors' => $errors]);
            exit;
        
        // Clipboard operations (Ctrl+C, Ctrl+X, Ctrl+P)
        case 'clipboard_copy':
            $items = json_decode($_POST['items'] ?? '[]', true);
            $sourcePath = sanitizePath($_POST['source_path'] ?? '');
            $destination = sanitizePath($_POST['destination'] ?? '');
            $processed = [];
            $errors = [];
            
            $sourceFullPath = getFullPath($sourcePath);
            $destFullPath = getFullPath($destination);
            
            foreach ($items as $name) {
                $name = basename($name);
                $srcFile = $sourceFullPath . '/' . $name;
                $destFile = $destFullPath . '/' . $name;
                
                // Handle duplicate names
                if (file_exists($destFile)) {
                    $info = pathinfo($name);
                    $baseName = $info['filename'];
                    $ext = isset($info['extension']) ? '.' . $info['extension'] : '';
                    $counter = 1;
                    while (file_exists($destFullPath . '/' . $baseName . '_copy' . $counter . $ext)) {
                        $counter++;
                    }
                    $destFile = $destFullPath . '/' . $baseName . '_copy' . $counter . $ext;
                }
                
                if (file_exists($srcFile)) {
                    if (copyRecursive($srcFile, $destFile)) {
                        $processed[] = $name;
                        auditLog('CLIPBOARD_COPY', "Copied: $name from $sourcePath to $destination");
                    } else {
                        $errors[] = "$name: Copy failed";
                    }
                } else {
                    $errors[] = "$name: Source not found";
                }
            }
            echo json_encode(['success' => count($processed) > 0, 'processed' => $processed, 'errors' => $errors]);
            exit;
            
        case 'clipboard_move':
            $items = json_decode($_POST['items'] ?? '[]', true);
            $sourcePath = sanitizePath($_POST['source_path'] ?? '');
            $destination = sanitizePath($_POST['destination'] ?? '');
            $processed = [];
            $errors = [];
            
            $sourceFullPath = getFullPath($sourcePath);
            $destFullPath = getFullPath($destination);
            
            // Don't move if source and destination are the same
            if ($sourceFullPath === $destFullPath) {
                echo json_encode(['success' => false, 'error' => 'Source and destination are the same']);
                exit;
            }
            
            foreach ($items as $name) {
                $name = basename($name);
                $srcFile = $sourceFullPath . '/' . $name;
                $destFile = $destFullPath . '/' . $name;
                
                // Handle duplicate names
                if (file_exists($destFile)) {
                    $info = pathinfo($name);
                    $baseName = $info['filename'];
                    $ext = isset($info['extension']) ? '.' . $info['extension'] : '';
                    $counter = 1;
                    while (file_exists($destFullPath . '/' . $baseName . '_' . $counter . $ext)) {
                        $counter++;
                    }
                    $destFile = $destFullPath . '/' . $baseName . '_' . $counter . $ext;
                }
                
                if (file_exists($srcFile)) {
                    if (rename($srcFile, $destFile)) {
                        $processed[] = $name;
                        auditLog('CLIPBOARD_MOVE', "Moved: $name from $sourcePath to $destination");
                    } else {
                        $errors[] = "$name: Move failed";
                    }
                } else {
                    $errors[] = "$name: Source not found";
                }
            }
            echo json_encode(['success' => count($processed) > 0, 'processed' => $processed, 'errors' => $errors]);
            exit;
            
        case 'bulk_compress':
            $items = json_decode($_POST['items'] ?? '[]', true);
            $archiveName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $_POST['name'] ?? 'archive');
            $format = $_POST['format'] ?? 'zip';
            if (empty($items)) {
                echo json_encode(['success' => false, 'error' => 'No items selected']);
                exit;
            }
            $tempDir = sys_get_temp_dir() . '/fm_compress_' . uniqid();
            mkdir($tempDir, 0755, true);
            foreach ($items as $name) {
                $name = basename($name);
                $sourcePath = $fullPath . '/' . $name;
                if (file_exists($sourcePath)) {
                    copyRecursive($sourcePath, $tempDir . '/' . $name);
                }
            }
            $archivePath = $fullPath . '/' . $archiveName;
            if ($format === 'zip') {
                $archivePath .= '.zip';
                $success = createZip($tempDir, $archivePath);
            } else {
                $archivePath .= '.tar.gz';
                $success = createTar($tempDir, $archivePath);
            }
            deleteRecursive($tempDir);
            if ($success) {
                echo json_encode(['success' => true, 'archive' => basename($archivePath)]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Compression failed']);
            }
            exit;
        case 'get_file_content':
            $name = basename($_POST['name'] ?? '');
            if ($name) {
                $targetPath = $fullPath . '/' . $name;
                if (file_exists($targetPath) && is_file($targetPath)) {
                    $size = filesize($targetPath);
                    if ($size > $CONFIG['max_edit_size']) {
                        echo json_encode(['success' => false, 'error' => 'File too large to edit (max ' . formatBytes($CONFIG['max_edit_size']) . ')']);
                    } else {
                        $content = file_get_contents($targetPath);
                        echo json_encode([
                            'success' => true,
                            'content' => $content,
                            'mode' => getAceMode($name),
                            'size' => $size,
                        ]);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'File not found']);
                }
            }
            exit;
        case 'save_file':
            $name = basename($_POST['name'] ?? '');
            $content = $_POST['content'] ?? '';
            $contentB64 = $_POST['content_b64'] ?? null;
            if (is_string($contentB64) && $contentB64 !== '') {
                $decoded = base64_decode($contentB64, true);
                if ($decoded === false) {
                    echo json_encode(['success' => false, 'error' => 'Invalid content encoding']);
                    exit;
                }
                $content = $decoded;
            }
            if ($name) {
                $targetPath = $fullPath . '/' . $name;
                
                // Check write permission
                $permCheck = checkOperationPermission($targetPath, 'write');
                if (!$permCheck['allowed']) {
                    echo json_encode(['success' => false, 'error' => $permCheck['error']]);
                    exit;
                }
                
                if (file_exists($targetPath) && is_file($targetPath)) {
                    // Clear any stat cache before checking
                    clearstatcache(true, $targetPath);
                    
                    // Double check actual writability
                    if (!is_writable($targetPath)) {
                        $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($targetPath))['name'] ?? fileowner($targetPath) : fileowner($targetPath);
                        $webUser = function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] ?? 'web server' : 'web server';
                        echo json_encode([
                            'success' => false, 
                            'error' => "File is not writable. Owner: $owner, Web server: $webUser. Use SSH to run: chmod 664 " . escapeshellarg($targetPath)
                        ]);
                        exit;
                    }
                    
                    // Try to save with error suppression and capture
                    $result = @file_put_contents($targetPath, $content);
                    
                    if ($result !== false) {
                        auditLog('FILE_SAVE', "Saved: $name in $currentPath");
                        echo json_encode(['success' => true]);
                    } else {
                        $lastError = error_get_last();
                        $errorMsg = $lastError ? $lastError['message'] : 'Unknown error';
                        
                        // Check for common server restrictions
                        $suggestions = [];
                        if (stripos($errorMsg, 'open_basedir') !== false) {
                            $suggestions[] = 'Server has open_basedir restriction';
                        }
                        if (stripos($errorMsg, 'permission') !== false || stripos($errorMsg, 'denied') !== false) {
                            $suggestions[] = 'Use SSH: chmod 666 ' . basename($targetPath);
                        }
                        
                        $fullError = 'Failed to save: ' . $errorMsg;
                        if (!empty($suggestions)) {
                            $fullError .= ' | Fix: ' . implode(', ', $suggestions);
                        }
                        
                        echo json_encode(['success' => false, 'error' => $fullError]);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'File not found']);
                }
            }
            exit;
        case 'compress':
            $name = basename($_POST['name'] ?? '');
            $format = $_POST['format'] ?? 'zip';
            if ($name) {
                $sourcePath = $fullPath . '/' . $name;
                $archiveName = pathinfo($name, PATHINFO_FILENAME);
                if ($format === 'zip') {
                    $archivePath = $fullPath . '/' . $archiveName . '.zip';
                    $success = createZip($sourcePath, $archivePath);
                } else {
                    $archivePath = $fullPath . '/' . $archiveName . '.tar.gz';
                    $success = createTar($sourcePath, $archivePath);
                }
                if ($success) {
                    echo json_encode(['success' => true, 'archive' => basename($archivePath)]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Compression failed']);
                }
            }
            exit;
        case 'extract':
            $name = basename($_POST['name'] ?? '');
            if ($name) {
                $archivePath = $fullPath . '/' . $name;
                $extractName = pathinfo($name, PATHINFO_FILENAME);
                $extractPath = $fullPath . '/' . $extractName;
                if (!file_exists($extractPath)) {
                    mkdir($extractPath, 0755, true);
                }
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if ($ext === 'zip') {
                    $success = extractZip($archivePath, $extractPath);
                } else {
                    $success = extractTar($archivePath, $extractPath);
                }
                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Extraction failed']);
                }
            }
            exit;
        case 'backup':
            $name = basename($_POST['name'] ?? '');
            if ($name) {
                $targetPath = $fullPath . '/' . $name;
                $backupDir = $fullPath . '/.backups';
                $backup = createBackup($targetPath, $backupDir);
                if ($backup) {
                    echo json_encode(['success' => true, 'backup' => basename($backup)]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Backup failed']);
                }
            }
            exit;
        case 'search':
            $query = $_POST['query'] ?? '';
            if (strlen($query) >= 2) {
                $results = searchFiles($fullPath, $query);
                echo json_encode(['success' => true, 'results' => $results]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Query too short']);
            }
            exit;
        case 'get_folder_tree':
            $scanRoot = $userRoot;
            $folders = [];
            try {
                if ($scanRoot === '/' || $scanRoot === '\\') {
                    $commonDirs = ['/var/www', '/home', '/opt', '/srv', '/usr/share', __DIR__];
                    foreach ($commonDirs as $commonDir) {
                        if (is_dir($commonDir) && is_readable($commonDir)) {
                            $relativePath = $commonDir;
                            $folders[] = [
                                'name' => basename($commonDir) ?: $commonDir,
                                'path' => $relativePath,
                                'depth' => 0,
                            ];
                            try {
                                $subItems = @scandir($commonDir);
                                if ($subItems !== false) {
                                    foreach ($subItems as $subItem) {
                                        if ($subItem === '.' || $subItem === '..') continue;
                                        $subPath = $commonDir . '/' . $subItem;
                                        if (is_dir($subPath) && is_readable($subPath) && !shouldExclude($subItem, true)) {
                                            $folders[] = [
                                                'name' => $subItem,
                                                'path' => $subPath,
                                                'depth' => 1,
                                            ];
                                        }
                                    }
                                }
                            } catch (Exception $e) {}
                        }
                    }
                } else {
                    $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($scanRoot, RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS),
                        RecursiveIteratorIterator::SELF_FIRST
                    );
                    $iterator->setMaxDepth(4);
                    foreach ($iterator as $file) {
                        try {
                            if ($file->isDir() && !shouldExclude($file->getFilename(), true)) {
                                $relativePath = str_replace($userRoot . '/', '', $file->getPathname());
                                $relativePath = str_replace($userRoot, '', $relativePath);
                                if ($relativePath) {
                                    $folders[] = [
                                        'name' => $file->getFilename(),
                                        'path' => $relativePath,
                                        'depth' => $iterator->getDepth(),
                                    ];
                                }
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
            } catch (Exception $e) {
                try {
                    $items = @scandir($scanRoot);
                    if ($items !== false) {
                        foreach ($items as $item) {
                            if ($item === '.' || $item === '..') continue;
                            $itemPath = $scanRoot . '/' . $item;
                            if (is_dir($itemPath) && !shouldExclude($item, true)) {
                                $folders[] = [
                                    'name' => $item,
                                    'path' => $item,
                                    'depth' => 0,
                                ];
                            }
                        }
                    }
                } catch (Exception $e2) {}
            }
            usort($folders, function($a, $b) {
                return strcmp($a['path'], $b['path']);
            });
            echo json_encode(['success' => true, 'folders' => $folders]);
            exit;
        case 'toggle_hidden':
            $_SESSION['show_hidden'] = !($_SESSION['show_hidden'] ?? $CONFIG['show_hidden']);
            echo json_encode(['success' => true, 'show_hidden' => $_SESSION['show_hidden']]);
            exit;
        case 'get_users':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $users = getActiveUsers();
            $userList = [];
            foreach ($users as $username => $user) {
                $userList[] = [
                    'username' => $username,
                    'root' => $user['root'],
                    'is_admin' => $user['is_admin'] ?? false,
                    'source' => $user['source'] ?? 'default',
                ];
            }
            echo json_encode(['success' => true, 'users' => $userList]);
            exit;
        case 'create_user':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $root = trim($_POST['root'] ?? __DIR__);
            $isAdmin = filter_var($_POST['is_admin'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if (strlen($username) < 3 || strlen($username) > 32) {
                echo json_encode(['success' => false, 'error' => 'Username must be 3-32 characters']);
                exit;
            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                echo json_encode(['success' => false, 'error' => 'Username can only contain letters, numbers, and underscores']);
                exit;
            }
            if (strlen($password) < 6) {
                echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
                exit;
            }
            if (createUser($username, $password, $root, $isAdmin)) {
                echo json_encode(['success' => true, 'message' => "User '$username' created successfully"]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create user (may already exist)']);
            }
            exit;
        case 'update_user':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? null;
            $root = isset($_POST['root']) ? trim($_POST['root']) : null;
            $isAdmin = isset($_POST['is_admin']) ? filter_var($_POST['is_admin'], FILTER_VALIDATE_BOOLEAN) : null;
            if (empty($username)) {
                echo json_encode(['success' => false, 'error' => 'Username required']);
                exit;
            }
            if ($password === '') {
                $password = null;
            }
            if ($password !== null && strlen($password) < 6) {
                echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
                exit;
            }
            if ($username === ($_SESSION['username'] ?? '') && $isAdmin === false) {
                echo json_encode(['success' => false, 'error' => 'Cannot remove your own admin privileges']);
                exit;
            }
            if (updateUser($username, $password, $root, $isAdmin)) {
                echo json_encode(['success' => true, 'message' => "User '$username' updated successfully"]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update user']);
            }
            exit;
        case 'delete_user':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $username = trim($_POST['username'] ?? '');
            if (empty($username)) {
                echo json_encode(['success' => false, 'error' => 'Username required']);
                exit;
            }
            if ($username === ($_SESSION['username'] ?? '')) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
                exit;
            }
            if (deleteUser($username)) {
                echo json_encode(['success' => true, 'message' => "User '$username' deleted successfully"]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
            }
            exit;
        case 'create_share':
            $name = basename($_POST['name'] ?? '');
            $expiry = $_POST['expiry'] ?? '24h';
            if ($name && $CONFIG['share_enabled']) {
                $targetPath = $fullPath . '/' . $name;
                if (file_exists($targetPath) && is_file($targetPath)) {
                    $share = createShareLink($targetPath, $expiry);
                    if ($share) {
                        $shareUrl = $CONFIG['base_url'] . '/filemanager.php?share=' . $share['token'];
                        echo json_encode([
                            'success' => true,
                            'share_url' => $shareUrl,
                            'token' => $share['token'],
                            'expires_at' => $share['expires_at'] ? date('Y-m-d H:i:s', $share['expires_at']) : null,
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to create share link']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'File not found']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Sharing is disabled']);
            }
            exit;
        case 'get_shares':
            if ($CONFIG['share_enabled']) {
                $shares = getSharesData();
                $result = [];
                foreach ($shares as $token => $share) {
                    if (isAdmin() || $share['created_by'] === ($_SESSION['username'] ?? '')) {
                        $result[] = [
                            'token' => $share['token'],
                            'file_name' => $share['file_name'],
                            'created_at' => date('Y-m-d H:i:s', $share['created_at']),
                            'expires_at' => $share['expires_at'] ? date('Y-m-d H:i:s', $share['expires_at']) : null,
                            'downloads' => $share['downloads'],
                            'is_valid' => isShareValid($share),
                            'share_url' => $CONFIG['base_url'] . '/filemanager.php?share=' . $share['token'],
                        ];
                    }
                }
                echo json_encode(['success' => true, 'shares' => $result]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Sharing is disabled']);
            }
            exit;
        case 'delete_share':
            $token = $_POST['token'] ?? '';
            if ($token && $CONFIG['share_enabled']) {
                $share = getShareByToken($token);
                if ($share && (isAdmin() || $share['created_by'] === ($_SESSION['username'] ?? ''))) {
                    if (deleteShare($token)) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to delete share']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Share not found or access denied']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
            }
            exit;
        case 'cleanup_shares':
            if (isAdmin() && $CONFIG['share_enabled']) {
                $count = cleanupExpiredShares();
                echo json_encode(['success' => true, 'cleaned' => $count]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Access denied']);
            }
            exit;
        case 'scan_file':
            $name = basename($_POST['name'] ?? '');
            if ($name) {
                $targetPath = $fullPath . '/' . $name;
                $result = scanFileForMalware($targetPath);
                echo json_encode(['success' => true, 'result' => $result]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No file specified']);
            }
            exit;
        case 'scan_directory':
            if (isAdmin()) {
                $scanPath = $fullPath ?: $userRoot;
                $result = scanDirectoryForMalware($scanPath);
                echo json_encode(['success' => true, 'result' => $result]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
            }
            exit;
        case 'scan_all':
            if (isAdmin()) {
                $result = scanDirectoryForMalware($userRoot, 1000);
                echo json_encode(['success' => true, 'result' => $result]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
            }
            exit;
        case 'get_audit_log':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $lines = (int)($_POST['lines'] ?? 100);
            $lines = min(max($lines, 10), 500);
            $logContent = '';
            
            if (file_exists(FM_AUDIT_LOG)) {
                $file = new SplFileObject(FM_AUDIT_LOG, 'r');
                $file->seek(PHP_INT_MAX);
                $totalLines = $file->key();
                
                $startLine = max(0, $totalLines - $lines);
                $logLines = [];
                
                $file->seek($startLine);
                while (!$file->eof()) {
                    $line = $file->fgets();
                    if (trim($line)) {
                        $logLines[] = htmlspecialchars($line);
                    }
                }
                
                $logContent = implode('', array_reverse($logLines));
            }
            
            echo json_encode(['success' => true, 'log' => $logContent, 'file' => FM_AUDIT_LOG]);
            exit;
        case 'clear_audit_log':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $backupName = FM_AUDIT_LOG . '.' . date('Y-m-d_H-i-s') . '.bak';
            if (file_exists(FM_AUDIT_LOG)) {
                rename(FM_AUDIT_LOG, $backupName);
            }
            auditLog('AUDIT_LOG_CLEARED', 'Audit log was cleared and backed up');
            echo json_encode(['success' => true, 'backup' => $backupName]);
            exit;
        case 'change_password':
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'error' => 'Not logged in']);
                exit;
            }
            
            $username = $_SESSION['username'];
            $allUsers = getAllUsers();
            
            if (!isset($allUsers[$username])) {
                echo json_encode(['success' => false, 'error' => 'User not found']);
                exit;
            }
            
            if (!password_verify($currentPassword, $allUsers[$username]['password'])) {
                echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
                auditLog('PASSWORD_CHANGE_FAILED', 'Invalid current password', 'WARN');
                exit;
            }
            
            if ($newPassword !== $confirmPassword) {
                echo json_encode(['success' => false, 'error' => 'New passwords do not match']);
                exit;
            }
            
            // Password strength check
            $minLength = $CONFIG['min_password_length'] ?? 8;
            if (strlen($newPassword) < $minLength) {
                echo json_encode(['success' => false, 'error' => "Password must be at least $minLength characters"]);
                exit;
            }
            
            if ($CONFIG['require_strong_passwords'] ?? true) {
                if (!preg_match('/[A-Z]/', $newPassword)) {
                    echo json_encode(['success' => false, 'error' => 'Password must contain at least one uppercase letter']);
                    exit;
                }
                if (!preg_match('/[a-z]/', $newPassword)) {
                    echo json_encode(['success' => false, 'error' => 'Password must contain at least one lowercase letter']);
                    exit;
                }
                if (!preg_match('/[0-9]/', $newPassword)) {
                    echo json_encode(['success' => false, 'error' => 'Password must contain at least one number']);
                    exit;
                }
            }
            
            if (updateUser($username, $newPassword, null, null)) {
                // Remove must_change_password flag
                $users = getDynamicUsers();
                if (isset($users[$username]['must_change_password'])) {
                    unset($users[$username]['must_change_password']);
                    saveDynamicUsers($users);
                }
                unset($_SESSION['must_change_password']);
                
                auditLog('PASSWORD_CHANGED', 'User changed their password');
                echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to change password']);
            }
            exit;
        case 'get_security_status':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            
            $status = [
                'session_fingerprinting' => FM_SESSION_FINGERPRINT,
                'rate_limiting_enabled' => true,
                'max_login_attempts' => FM_MAX_LOGIN_ATTEMPTS,
                'lockout_time' => FM_LOCKOUT_TIME,
                'session_timeout' => $CONFIG['session_timeout'],
                'audit_logging' => $CONFIG['audit_enabled'] ?? true,
                'external_config' => file_exists(FM_CONFIG_FILE),
                'strong_passwords' => $CONFIG['require_strong_passwords'] ?? true,
                'blocked_extensions' => $CONFIG['blocked_extensions'],
                'https_enabled' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            ];
            
            // Get blocked IPs count
            $attempts = getLoginAttempts();
            $blockedIPs = array_filter($attempts, function($a) {
                return isset($a['locked_until']) && time() < $a['locked_until'];
            });
            $status['blocked_ips_count'] = count($blockedIPs);
            
            echo json_encode(['success' => true, 'status' => $status]);
            exit;
        case 'unblock_ip':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $ip = $_POST['ip'] ?? '';
            if ($ip) {
                $attempts = getLoginAttempts();
                if (isset($attempts[$ip])) {
                    unset($attempts[$ip]);
                    saveLoginAttempts($attempts);
                    auditLog('IP_UNBLOCKED', "Admin unblocked IP: $ip");
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'IP not found']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'IP address required']);
            }
            exit;
        case 'get_blocked_ips':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $attempts = getLoginAttempts();
            $blocked = [];
            foreach ($attempts as $ip => $data) {
                if (isset($data['locked_until']) && time() < $data['locked_until']) {
                    $blocked[] = [
                        'ip' => $ip,
                        'attempts' => $data['count'],
                        'locked_until' => date('Y-m-d H:i:s', $data['locked_until']),
                        'remaining' => $data['locked_until'] - time()
                    ];
                }
            }
            echo json_encode(['success' => true, 'blocked' => $blocked]);
            exit;
        case 'save_config':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            
            $newConfig = [];
            
            // Only allow updating specific settings
            if (isset($_POST['session_timeout'])) {
                $newConfig['session_timeout'] = max(300, min(86400, (int)$_POST['session_timeout']));
            }
            if (isset($_POST['audit_enabled'])) {
                $newConfig['audit_enabled'] = filter_var($_POST['audit_enabled'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($_POST['require_strong_passwords'])) {
                $newConfig['require_strong_passwords'] = filter_var($_POST['require_strong_passwords'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($_POST['min_password_length'])) {
                $newConfig['min_password_length'] = max(6, min(32, (int)$_POST['min_password_length']));
            }
            
            if (!empty($newConfig)) {
                $existingConfig = file_exists(FM_CONFIG_FILE) ? json_decode(file_get_contents(FM_CONFIG_FILE), true) ?? [] : [];
                $mergedConfig = array_merge($existingConfig, $newConfig);
                
                if (file_put_contents(FM_CONFIG_FILE, json_encode($mergedConfig, JSON_PRETTY_PRINT))) {
                    @chmod(FM_CONFIG_FILE, 0600);
                    auditLog('CONFIG_UPDATED', 'Configuration updated: ' . json_encode($newConfig));
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save configuration']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'No settings to update']);
            }
            exit;
        case 'install_phpmyadmin':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $pmaVersion = '5.2.3';
            $pmaUrl = "https://files.phpmyadmin.net/phpMyAdmin/{$pmaVersion}/phpMyAdmin-{$pmaVersion}-all-languages.zip";
            $pmaZip = $userRoot . '/phpmyadmin.zip';
            $pmaDir = $userRoot . '/phpmyadmin';
            if (is_dir($pmaDir) && file_exists($pmaDir . '/index.php')) {
                $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                $pmaPath = ($scriptDir === '/' || $scriptDir === '\\') ? '/phpmyadmin/index.php' : $scriptDir . '/phpmyadmin/index.php';
                echo json_encode([
                    'success' => true, 
                    'status' => 'already_installed',
                    'message' => 'phpMyAdmin is already installed',
                    'url' => $pmaPath
                ]);
                exit;
            }
            $downloadSuccess = false;
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $pmaUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_CONNECTTIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/octet-stream,application/zip,*/*',
                        'Accept-Encoding: identity',
                    ],
                ]);
                $content = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);
                if ($content !== false && $httpCode >= 200 && $httpCode < 400 && strlen($content) > 10000) {
                    $downloadSuccess = true;
                }
            }
            if (!$downloadSuccess) {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 300,
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'follow_location' => true,
                        'max_redirects' => 5,
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ]);
                $content = @file_get_contents($pmaUrl, false, $context);
                if ($content !== false && strlen($content) > 10000) {
                    $downloadSuccess = true;
                }
            }
            if (!$downloadSuccess || empty($content)) {
                $errorMsg = 'Failed to download phpMyAdmin.';
                if (isset($curlError) && $curlError) {
                    $errorMsg .= ' cURL error: ' . $curlError;
                }
                if (!function_exists('curl_init')) {
                    $errorMsg .= ' cURL extension not available. Please enable it in PHP.';
                }
                if (!ini_get('allow_url_fopen')) {
                    $errorMsg .= ' allow_url_fopen is disabled.';
                }
                echo json_encode(['success' => false, 'error' => $errorMsg]);
                exit;
            }
            if (file_put_contents($pmaZip, $content) === false) {
                echo json_encode(['success' => false, 'error' => 'Failed to save phpMyAdmin zip file. Check write permissions.']);
                exit;
            }
            if (!class_exists('ZipArchive')) {
                @unlink($pmaZip);
                echo json_encode(['success' => false, 'error' => 'ZipArchive extension not available']);
                exit;
            }
            $zip = new ZipArchive();
            if ($zip->open($pmaZip) !== true) {
                @unlink($pmaZip);
                echo json_encode(['success' => false, 'error' => 'Failed to open zip file']);
                exit;
            }
            $tempDir = $userRoot . '/phpmyadmin_temp_' . time();
            if (!$zip->extractTo($tempDir)) {
                $zip->close();
                @unlink($pmaZip);
                echo json_encode(['success' => false, 'error' => 'Failed to extract zip file']);
                exit;
            }
            $zip->close();
            $extractedDir = null;
            $items = scandir($tempDir);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..' && is_dir($tempDir . '/' . $item)) {
                    $extractedDir = $tempDir . '/' . $item;
                    break;
                }
            }
            if (!$extractedDir) {
                @unlink($pmaZip);
                deleteRecursive($tempDir);
                echo json_encode(['success' => false, 'error' => 'Failed to find extracted phpMyAdmin folder']);
                exit;
            }
            if (!rename($extractedDir, $pmaDir)) {
                @unlink($pmaZip);
                deleteRecursive($tempDir);
                echo json_encode(['success' => false, 'error' => 'Failed to move phpMyAdmin to final location']);
                exit;
            }
            $configContent = '<?php
$cfg[\'blowfish_secret\'] = \'' . bin2hex(random_bytes(16)) . '\';
$i = 0;
$i++;
$cfg[\'Servers\'][$i][\'auth_type\'] = \'cookie\';
$cfg[\'Servers\'][$i][\'host\'] = \'localhost\';
$cfg[\'Servers\'][$i][\'compress\'] = false;
$cfg[\'Servers\'][$i][\'AllowNoPassword\'] = false;
$cfg[\'UploadDir\'] = \'\';
$cfg[\'SaveDir\'] = \'\';
$cfg[\'TempDir\'] = sys_get_temp_dir();
';
            file_put_contents($pmaDir . '/config.inc.php', $configContent);
            @unlink($pmaZip);
            @rmdir($tempDir);
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $pmaPath = ($scriptDir === '/' || $scriptDir === '\\') ? '/phpmyadmin/index.php' : $scriptDir . '/phpmyadmin/index.php';
            echo json_encode([
                'success' => true, 
                'message' => 'phpMyAdmin installed successfully!',
                'url' => $pmaPath
            ]);
            exit;
        case 'check_phpmyadmin':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $pmaDir = $userRoot . '/phpmyadmin';
            if (is_dir($pmaDir) && file_exists($pmaDir . '/index.php')) {
                $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                $pmaPath = ($scriptDir === '/' || $scriptDir === '\\') ? '/phpmyadmin/index.php' : $scriptDir . '/phpmyadmin/index.php';
                echo json_encode([
                    'success' => true, 
                    'installed' => true,
                    'url' => $pmaPath
                ]);
            } else {
                echo json_encode(['success' => true, 'installed' => false]);
            }
            exit;
        case 'uninstall_phpmyadmin':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Admin access required']);
                exit;
            }
            $pmaDir = $userRoot . '/phpmyadmin';
            if (is_dir($pmaDir)) {
                if (deleteRecursive($pmaDir)) {
                    echo json_encode(['success' => true, 'message' => 'phpMyAdmin uninstalled successfully']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to remove phpMyAdmin directory']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'phpMyAdmin is not installed']);
            }
            exit;
            
        case 'get_permissions':
            $name = basename($_POST['name'] ?? '');
            if ($name) {
                $targetPath = $fullPath . '/' . $name;
                if (file_exists($targetPath)) {
                    $info = getPermissionInfo($targetPath);
                    $info['name'] = $name;
                    $info['path'] = $currentPath ? $currentPath . '/' . $name : $name;
                    $info['is_dir'] = is_dir($targetPath);
                    $info['size'] = is_dir($targetPath) ? 0 : filesize($targetPath);
                    $info['modified'] = date('Y-m-d H:i:s', filemtime($targetPath));
                    
                    // Get numeric owner/group IDs
                    $info['owner_id'] = @fileowner($targetPath);
                    $info['group_id'] = @filegroup($targetPath);
                    
                    // Get current web server user
                    $info['web_user'] = 'unknown';
                    $info['web_user_id'] = null;
                    if (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
                        $processUid = posix_getuid();
                        $processInfo = posix_getpwuid($processUid);
                        $info['web_user'] = $processInfo['name'] ?? (string)$processUid;
                        $info['web_user_id'] = $processUid;
                    }
                    
                    // Check what operations are allowed
                    $info['can_read'] = checkOperationPermission($targetPath, 'read')['allowed'];
                    $info['can_write'] = checkOperationPermission($targetPath, 'write')['allowed'];
                    $info['can_delete'] = checkOperationPermission($targetPath, 'delete')['allowed'];
                    $info['can_rename'] = checkOperationPermission($targetPath, 'rename')['allowed'];
                    
                    // Check if chmod is likely to work (user owns the file or is admin)
                    $info['chmod_possible'] = false;
                    $info['is_owner'] = false;
                    if (function_exists('posix_getuid') && function_exists('fileowner')) {
                        $currentUid = posix_getuid();
                        $fileOwner = @fileowner($targetPath);
                        $info['is_owner'] = ($currentUid === $fileOwner);
                        $info['chmod_possible'] = ($currentUid === 0 || $currentUid === $fileOwner);
                    } else {
                        // On Windows or if posix not available, try chmod anyway
                        $info['chmod_possible'] = true;
                    }
                    
                    // Generate suggested chown commands
                    $relativePath = escapeshellarg($currentPath ? $currentPath . '/' . $name : $name);
                    $info['chown_commands'] = [
                        'single' => "chown {$info['web_user']}:{$info['web_user']} {$relativePath}",
                        'recursive' => "chown -R {$info['web_user']}:{$info['web_user']} {$relativePath}",
                        'group_only' => "chgrp {$info['web_user']} {$relativePath}",
                    ];
                    
                    echo json_encode(['success' => true, 'permissions' => $info]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'File not found']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'No file specified']);
            }
            exit;
            
        case 'attempt_chmod':
            if (!isAdmin()) {
                echo json_encode(['success' => false, 'error' => 'Administrator access required to change permissions']);
                exit;
            }
            
            $name = basename($_POST['name'] ?? '');
            $mode = $_POST['mode'] ?? '';
            $recursive = ($_POST['recursive'] ?? '') === '1';
            $fileModeInput = $_POST['file_mode'] ?? '';
            
            if (!$name) {
                echo json_encode(['success' => false, 'error' => 'No file specified']);
                exit;
            }
            
            // Validate mode - accept octal string (e.g., "755", "0755", "644")
            $mode = ltrim($mode, '0');
            if (!preg_match('/^[0-7]{3,4}$/', $mode)) {
                echo json_encode(['success' => false, 'error' => 'Invalid permission mode. Use octal format like 755 or 644']);
                exit;
            }
            
            $targetPath = $fullPath . '/' . $name;
            if (!file_exists($targetPath)) {
                echo json_encode(['success' => false, 'error' => 'File not found']);
                exit;
            }
            
            // Convert to octal integer
            $modeOctal = octdec($mode);
            
            // For recursive, determine file mode (defaults to 644 if dir mode is provided)
            $fileModeOctal = $modeOctal;
            if ($recursive && is_dir($targetPath)) {
                if ($fileModeInput && preg_match('/^[0-7]{3,4}$/', ltrim($fileModeInput, '0'))) {
                    $fileModeOctal = octdec(ltrim($fileModeInput, '0'));
                } else {
                    // Default: if dir is 755, files are 644; if dir is 777, files are 666
                    $fileModeOctal = ($modeOctal & 0111) ? ($modeOctal & ~0111) : $modeOctal;
                }
            }
            
            // Attempt chmod
            $oldPerms = substr(sprintf('%o', fileperms($targetPath)), -4);
            
            if ($recursive && is_dir($targetPath)) {
                // Recursive chmod
                $recursiveResult = chmodRecursive($targetPath, $modeOctal, $fileModeOctal);
                
                if ($recursiveResult['success'] > 0) {
                    $successCount = $recursiveResult['success'];
                    $failedCount = $recursiveResult['failed'];
                    
                    clearstatcache(true, $targetPath);
                    $newPerms = substr(sprintf('%o', fileperms($targetPath)), -4);
                    
                    $message = "Recursive chmod completed: {$successCount} item(s) changed";
                    if ($failedCount > 0) {
                        $message .= ", {$failedCount} failed";
                    }
                    
                    auditLog('CHMOD_RECURSIVE_SUCCESS', "Recursive chmod on '{$name}': {$successCount} success, {$failedCount} failed");
                    
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'old_perms' => $oldPerms,
                        'new_perms' => $newPerms,
                        'recursive_stats' => [
                            'success' => $successCount,
                            'failed' => $failedCount,
                            'errors' => array_slice($recursiveResult['errors'], 0, 5) // Limit errors shown
                        ]
                    ]);
                } else {
                    auditLog('CHMOD_RECURSIVE_FAILED', "Recursive chmod failed on '{$name}'", 'WARN');
                    echo json_encode([
                        'success' => false,
                        'error' => 'Recursive chmod failed. No items were changed.',
                        'errors' => array_slice($recursiveResult['errors'], 0, 5)
                    ]);
                }
            } else {
                // Single file/folder chmod (original behavior)
                $result = @chmod($targetPath, $modeOctal);
                
                if ($result) {
                    clearstatcache(true, $targetPath);
                    $newPerms = substr(sprintf('%o', fileperms($targetPath)), -4);
                    
                    // Verify the change actually took effect
                    if ($newPerms !== $oldPerms || $newPerms === str_pad($mode, 4, '0', STR_PAD_LEFT)) {
                        auditLog('CHMOD_SUCCESS', "Changed permissions on '{$name}' from {$oldPerms} to {$newPerms}");
                        echo json_encode([
                            'success' => true, 
                            'message' => "Permissions changed successfully",
                            'old_perms' => $oldPerms,
                            'new_perms' => $newPerms
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false, 
                            'error' => 'chmod command executed but permissions did not change. The server may be overriding permission changes. Try using SSH or cPanel File Manager.',
                            'old_perms' => $oldPerms,
                            'current_perms' => $newPerms
                        ]);
                    }
                } else {
                    $error = error_get_last();
                    $errorMsg = $error ? $error['message'] : 'Unknown error';
                    
                    auditLog('CHMOD_FAILED', "Failed to change permissions on '{$name}': {$errorMsg}", 'WARN');
                    
                    // Provide helpful error message
                    $helpText = 'Permission denied by server. This typically happens when:
• The web server user does not own this file
• The hosting provider restricts chmod operations
• safe_mode or open_basedir restrictions are active

Try using SSH or cPanel File Manager to change permissions.';
                    
                    echo json_encode([
                        'success' => false, 
                        'error' => $helpText,
                        'technical_error' => $errorMsg
                    ]);
                }
            }
            exit;
            
        case 'chmod_recursive_stream':
            // SSE streaming chmod with real-time progress
            if (!isAdmin()) {
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                echo "data: " . json_encode(['type' => 'error', 'error' => 'Administrator access required']) . "\n\n";
                exit;
            }
            
            $name = basename($_POST['name'] ?? '');
            $mode = $_POST['mode'] ?? '';
            $fileModeInput = $_POST['file_mode'] ?? '';
            
            if (!$name) {
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                echo "data: " . json_encode(['type' => 'error', 'error' => 'No file specified']) . "\n\n";
                exit;
            }
            
            $mode = ltrim($mode, '0');
            if (!preg_match('/^[0-7]{3,4}$/', $mode)) {
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                echo "data: " . json_encode(['type' => 'error', 'error' => 'Invalid permission mode']) . "\n\n";
                exit;
            }
            
            $targetPath = $fullPath . '/' . $name;
            if (!file_exists($targetPath) || !is_dir($targetPath)) {
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                echo "data: " . json_encode(['type' => 'error', 'error' => 'Directory not found']) . "\n\n";
                exit;
            }
            
            // Set up SSE headers
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Disable nginx buffering
            
            // Disable output buffering
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', false);
            while (@ob_end_flush());
            ob_implicit_flush(true);
            
            $modeOctal = octdec($mode);
            $fileModeOctal = $modeOctal;
            if ($fileModeInput && preg_match('/^[0-7]{3,4}$/', ltrim($fileModeInput, '0'))) {
                $fileModeOctal = octdec(ltrim($fileModeInput, '0'));
            } else {
                $fileModeOctal = ($modeOctal & 0111) ? ($modeOctal & ~0111) : $modeOctal;
            }
            
            $oldPerms = substr(sprintf('%o', fileperms($targetPath)), -4);
            
            // Count total items first
            $total = countItemsRecursive($targetPath);
            echo "data: " . json_encode(['type' => 'start', 'total' => $total, 'name' => $name]) . "\n\n";
            @ob_flush();
            @flush();
            
            // Process with progress updates
            $processed = 0;
            $result = ['success' => 0, 'failed' => 0, 'errors' => []];
            chmodRecursiveWithProgress($targetPath, $modeOctal, $fileModeOctal, $total, $processed, $result);
            
            clearstatcache(true, $targetPath);
            $newPerms = substr(sprintf('%o', fileperms($targetPath)), -4);
            
            // Send completion event
            echo "data: " . json_encode([
                'type' => 'complete',
                'success' => $result['success'],
                'failed' => $result['failed'],
                'errors' => array_slice($result['errors'], 0, 5),
                'old_perms' => $oldPerms,
                'new_perms' => $newPerms
            ]) . "\n\n";
            @ob_flush();
            @flush();
            
            if ($result['success'] > 0) {
                auditLog('CHMOD_RECURSIVE_SUCCESS', "Recursive chmod on '{$name}': {$result['success']} success, {$result['failed']} failed");
            } else {
                auditLog('CHMOD_RECURSIVE_FAILED', "Recursive chmod failed on '{$name}'", 'WARN');
            }
            
            exit;
            
        case 'check_folder_permissions':
            // Check permissions for the current folder (for create/upload operations)
            $info = getPermissionInfo($fullPath);
            $info['path'] = $currentPath ?: '/';
            $info['can_create'] = checkOperationPermission($fullPath, 'create')['allowed'];
            $info['can_upload'] = checkOperationPermission($fullPath, 'upload')['allowed'];
            echo json_encode(['success' => true, 'permissions' => $info]);
            exit;
    }
}

if (isset($_GET['download'])) {
    $filename = basename($_GET['download']);
    $filepath = $fullPath . '/' . $filename;
    if (file_exists($filepath) && is_file($filepath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache');
        readfile($filepath);
        exit;
    }
}

if (isset($_GET['preview'])) {
    $filename = basename($_GET['preview']);
    $filepath = $fullPath . '/' . $filename;
    if (file_exists($filepath) && is_file($filepath)) {
        $mimeType = getMimeType($filename);
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=86400');
        readfile($filepath);
        exit;
    }
}

$items = [];
if (is_dir($fullPath)) {
    $rawItems = scandir($fullPath);
    foreach ($rawItems as $item) {
        if ($item === '.' || $item === '..') continue;
        $itemPath = $fullPath . '/' . $item;
        $isDir = is_dir($itemPath);
        if (shouldExclude($item, $isDir)) continue;
        $items[] = [
            'name' => $item,
            'is_dir' => $isDir,
            'size' => $isDir ? 0 : filesize($itemPath),
            'modified' => filemtime($itemPath),
            'perms' => substr(sprintf('%o', fileperms($itemPath)), -4),
            'perms_string' => getPermissionString($itemPath),
            'readable' => is_readable($itemPath),
            'writable' => is_writable($itemPath),
        ];
    }
    usort($items, function($a, $b) {
        if ($a['is_dir'] !== $b['is_dir']) {
            return $b['is_dir'] - $a['is_dir'];
        }
        return strcasecmp($a['name'], $b['name']);
    });
}

$breadcrumbs = [['name' => 'Root', 'path' => '']];
if ($currentPath) {
    $parts = explode('/', $currentPath);
    $path = '';
    foreach ($parts as $part) {
        $path .= ($path ? '/' : '') . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $path];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>File Manager Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ext-language_tools.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); min-height: 100vh; }
        .glass { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); }
        .file-item { transition: all 0.2s ease; }
        .file-item:hover { transform: translateY(-2px); }
        .file-item.selected { background: rgba(99, 102, 241, 0.2); border-color: rgba(99, 102, 241, 0.5); }
        .context-menu { position: fixed; z-index: 100; display: none; }
        .context-menu.active { display: block; }
        .modal { display: none; }
        .modal.active { display: flex; }
        .toast { transform: translateX(120%); transition: transform 0.3s ease; }
        .toast.active { transform: translateX(0); }
        .drop-zone.dragover { border-color: rgba(99, 102, 241, 0.8); background: rgba(99, 102, 241, 0.1); }
        .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
        .sidebar.open { transform: translateX(0); }
        #editor { width: 100%; height: 60vh; min-height: 400px; border-radius: 0.75rem; }
        #editorModal.fullscreen { padding: 0; }
        #editorModal.fullscreen .editor-panel { max-width: 100%; max-height: 100%; height: 100%; border-radius: 0; }
        #editorModal.fullscreen #editor { height: calc(100vh - 120px); min-height: unset; border-radius: 0; }
        .btn-action { @apply flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium transition-all; }
        .btn-primary { @apply bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white; }
        .btn-secondary { @apply bg-slate-700/50 hover:bg-slate-700 text-white border border-slate-600/50; }
        .btn-danger { @apply bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30; }
        @media (max-width: 640px) {
            .file-item { padding: 0.75rem; }
            .file-item .text-4xl { font-size: 2rem; }
        }
    </style>
</head>
<body class="text-white">
    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="toggleSidebar()"></div>
    <div id="sidebar" class="sidebar fixed top-0 left-0 h-full w-72 glass border-r border-slate-700/50 z-50 p-6 flex flex-col">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-bold">📂 File Manager</h2>
            <button onclick="toggleSidebar()" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto pb-4">
            <nav class="space-y-2">
                <a href="?" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors">
                    <span>🏠</span> Home
                </a>
                <button onclick="showModal('uploadModal'); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors text-left">
                    <span>⬆️</span> Upload Files
                </button>
                <button onclick="showModal('createFolderModal'); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors text-left">
                    <span>📁</span> New Folder
                </button>
                <button onclick="showModal('createFileModal'); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors text-left">
                    <span>📄</span> New File
                </button>
                <button onclick="toggleHiddenFiles(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors text-left">
                    <span><?= ($_SESSION['show_hidden'] ?? $CONFIG['show_hidden']) ? '🙈' : '👁️' ?></span>
                    <?= ($_SESSION['show_hidden'] ?? $CONFIG['show_hidden']) ? 'Hide Hidden Files' : 'Show Hidden Files' ?>
                </button>
                <?php if (isAdmin()): ?>
                <div class="border-t border-slate-700/50 my-4"></div>
                <button onclick="showSecurityDashboard(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600/20 text-green-400 transition-colors text-left">
                    <span>🔐</span> Security Dashboard
                </button>
                <button onclick="showAuditLogModal(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600/20 text-green-400 transition-colors text-left">
                    <span>📋</span> Audit Log
                </button>
                <button onclick="showPhpMyAdminModal(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-600/20 text-orange-400 transition-colors text-left">
                    <span>🐬</span> phpMyAdmin
                </button>
                <button onclick="scanAllFiles(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-600/20 text-orange-400 transition-colors text-left">
                    <span>🛡️</span> Security Scan
                </button>
                <button onclick="showManageSharesModal(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors text-left">
                    <span>🔗</span> Manage Shares
                </button>
                <button onclick="showSettingsModal(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors text-left">
                    <span>⚙️</span> User Settings
                </button>
                <?php endif; ?>
                <div class="border-t border-slate-700/50 my-4"></div>
                <button onclick="showChangePasswordModal(); toggleSidebar();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-colors text-left">
                    <span>🔑</span> Change Password
                </button>
            </nav>
        </div>
        <div class="flex-shrink-0 p-4 border-t border-slate-700/50">
            <div class="glass rounded-xl p-4 border border-slate-700/50 mb-4">
                <p class="text-sm text-slate-400">Logged in as</p>
                <p class="font-semibold"><?= htmlspecialchars($_SESSION['username'] ?? 'Unknown') ?></p>
                <?php if (isAdmin()): ?>
                <span class="inline-block mt-1 px-2 py-0.5 bg-indigo-600/30 text-indigo-400 text-xs rounded-full">Admin</span>
                <?php endif; ?>
            </div>
            <form method="POST" class="w-full">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-xl transition-colors">
                    <span>🚪</span> Logout
                </button>
            </form>
            <p class="text-center text-slate-600 text-xs mt-4">
                <a href="https://t.me/anusoni1024" target="_blank" rel="noopener noreferrer" 
                   class="text-cyan-500 hover:text-cyan-400 transition-colors">
                    Created by @anusoni1024 💬
                </a>
            </p>
        </div>
    </div>
    <header class="glass border-b border-slate-700/50 sticky top-0 z-30">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <button id="menuBtn" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <nav class="flex items-center gap-2 text-sm overflow-x-auto scrollbar-hide">
                        <?php foreach ($breadcrumbs as $i => $crumb): ?>
                            <?php if ($i > 0): ?>
                                <span class="text-slate-500">/</span>
                            <?php endif; ?>
                            <a href="?path=<?= urlencode($crumb['path']) ?>" 
                               class="hover:text-indigo-400 transition-colors whitespace-nowrap <?= $i === count($breadcrumbs) - 1 ? 'text-white font-medium' : 'text-slate-400' ?>">
                                <?= htmlspecialchars($crumb['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-2 glass rounded-xl border border-slate-700/50 p-1">
                        <button onclick="setView('grid')" id="viewGrid" class="p-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button onclick="setView('list')" id="viewList" class="p-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Clipboard Indicator -->
                    <div id="clipboardIndicator" class="hidden items-center gap-2 px-3 py-2 glass rounded-xl border border-slate-700/50 cursor-pointer hover:border-indigo-500/50 transition-all" onclick="showClipboardDetails()" title="Click to view clipboard contents">
                        <span id="clipboardIcon" class="text-lg">📋</span>
                        <span id="clipboardText" class="text-sm text-slate-300"></span>
                        <button onclick="event.stopPropagation(); clearClipboard();" class="ml-1 p-1 hover:bg-slate-700/50 rounded text-slate-400 hover:text-red-400 transition-colors" title="Clear clipboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Undo Indicator -->
                    <div id="undoIndicator" class="hidden items-center gap-2 px-3 py-2 glass rounded-xl border border-amber-500/30 cursor-pointer hover:border-amber-500/50 transition-all bg-amber-500/10" onclick="performUndo()" title="Click to undo or press Ctrl+Z">
                        <span class="text-lg">↩️</span>
                        <span id="undoText" class="text-sm text-amber-300"></span>
                    </div>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search..." 
                            class="w-32 sm:w-48 px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                            onkeyup="handleSearch(event)">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="container mx-auto px-4 py-6">
        <div id="bulkToolbar" class="hidden mb-4 glass rounded-xl p-4 border border-slate-700/50">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" 
                        class="w-5 h-5 rounded border-slate-600 bg-slate-800 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <span id="selectionCount" class="text-sm text-slate-400">0 selected</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="bulkDelete()" class="btn-action btn-danger">
                        <span>🗑️</span> Delete
                    </button>
                    <button onclick="showBulkMoveModal()" class="btn-action btn-secondary">
                        <span>📦</span> Move
                    </button>
                    <button onclick="showBulkCopyModal()" class="btn-action btn-secondary">
                        <span>📋</span> Copy
                    </button>
                    <button onclick="showBulkCompressModal()" class="btn-action btn-secondary">
                        <span>🗜️</span> Compress
                    </button>
                    <button onclick="clearSelection()" class="btn-action btn-secondary">
                        <span>✕</span> Clear
                    </button>
                </div>
            </div>
        </div>
        <div id="dropZone" class="drop-zone border-2 border-dashed border-slate-700/50 rounded-2xl p-8 mb-6 text-center hidden transition-all">
            <div class="text-4xl mb-2">📤</div>
            <p class="text-slate-400">Drop files here to upload</p>
        </div>
        <div id="fileGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <?php if (empty($items)): ?>
            <div class="col-span-full text-center py-16">
                <div class="text-6xl mb-4 opacity-50">📭</div>
                <p class="text-slate-400 text-lg">This folder is empty</p>
                <p class="text-slate-500 text-sm mt-2">Drop files here or use the upload button</p>
            </div>
            <?php endif; ?>
            <?php foreach ($items as $item): ?>
            <div class="file-item glass rounded-2xl p-4 cursor-pointer border border-slate-700/30 hover:border-slate-600/50 relative"
                 data-name="<?= htmlspecialchars($item['name']) ?>"
                 data-is-dir="<?= $item['is_dir'] ? '1' : '0' ?>"
                 data-size="<?= $item['size'] ?>"
                 data-modified="<?= $item['modified'] ?>"
                 data-readable="<?= $item['readable'] ? '1' : '0' ?>"
                 data-writable="<?= $item['writable'] ? '1' : '0' ?>"
                 data-perms="<?= htmlspecialchars($item['perms']) ?>"
                 ondblclick="handleItemDblClick(this)"
                 oncontextmenu="showContextMenu(event, this)"
                 onclick="selectItem(event, this)">
                <div class="absolute top-2 left-2 z-10">
                    <input type="checkbox" class="item-checkbox w-5 h-5 rounded border-slate-600 bg-slate-800/80 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                           data-name="<?= htmlspecialchars($item['name']) ?>"
                           data-isdir="<?= $item['is_dir'] ? 'true' : 'false' ?>"
                           onclick="event.stopPropagation(); toggleItemSelection(this)"
                           onchange="updateBulkToolbar()">
                </div>
                <!-- Permission Badge -->
                <div class="absolute top-2 right-2 z-10" title="<?= $item['readable'] && $item['writable'] ? 'Full Access' : ($item['readable'] ? 'Read Only' : 'No Access') ?> (<?= $item['perms'] ?>)">
                    <?php if ($item['readable'] && $item['writable']): ?>
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-600/30 text-green-400 text-xs" title="Full Access">✓</span>
                    <?php elseif ($item['readable']): ?>
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-600/30 text-yellow-400 text-xs" title="Read Only">📖</span>
                    <?php else: ?>
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-600/30 text-red-400 text-xs" title="No Access">🔒</span>
                    <?php endif; ?>
                </div>
                <div class="text-4xl mb-3 text-center">
                    <?php if ($item['is_dir']): ?>
                        📁
                    <?php elseif (isImage($item['name'])): ?>
                        <img src="?path=<?= urlencode($currentPath) ?>&preview=<?= urlencode($item['name']) ?>" 
                             alt="" class="w-16 h-16 mx-auto object-cover rounded-xl shadow-lg" loading="lazy">
                    <?php else: ?>
                        <?= getFileIcon($item['name']) ?>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium truncate" title="<?= htmlspecialchars($item['name']) ?>">
                        <?= htmlspecialchars($item['name']) ?>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        <?= $item['is_dir'] ? 'Folder' : formatBytes($item['size']) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="fileList" class="hidden">
            <table id="filesTable" class="w-full glass rounded-2xl overflow-hidden">
                <thead class="bg-slate-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase w-10">
                            <input type="checkbox" id="selectAllListCheckbox" onchange="toggleSelectAll(this)" 
                                class="w-5 h-5 rounded border-slate-600 bg-slate-800 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Size</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase hidden lg:table-cell">Permissions</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase hidden md:table-cell">Modified</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr class="border-t border-slate-700/30 hover:bg-slate-800/30 transition-colors file-item"
                        data-name="<?= htmlspecialchars($item['name']) ?>"
                        data-is-dir="<?= $item['is_dir'] ? '1' : '0' ?>"
                        data-readable="<?= $item['readable'] ? '1' : '0' ?>"
                        data-writable="<?= $item['writable'] ? '1' : '0' ?>"
                        data-perms="<?= htmlspecialchars($item['perms']) ?>"
                        ondblclick="handleItemDblClick(this)"
                        oncontextmenu="showContextMenu(event, this)">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="item-checkbox w-5 h-5 rounded border-slate-600 bg-slate-800 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                   data-name="<?= htmlspecialchars($item['name']) ?>"
                                   data-isdir="<?= $item['is_dir'] ? 'true' : 'false' ?>"
                                   onclick="event.stopPropagation(); toggleItemSelection(this)"
                                   onchange="updateBulkToolbar()">
                        </td>
                        <td class="px-4 py-3" onclick="selectItem(event, this.closest('tr'))">
                            <div class="flex items-center gap-3">
                                <span class="text-xl"><?= getFileIcon($item['name'], $item['is_dir']) ?></span>
                                <span class="truncate max-w-xs"><?= htmlspecialchars($item['name']) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-sm" onclick="selectItem(event, this.closest('tr'))">
                            <?= $item['is_dir'] ? '—' : formatBytes($item['size']) ?>
                        </td>
                        <td class="px-4 py-3 text-sm hidden lg:table-cell" onclick="selectItem(event, this.closest('tr'))">
                            <div class="flex items-center gap-2">
                                <?php if ($item['readable'] && $item['writable']): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-600/20 text-green-400 border border-green-500/30">
                                        <?= $item['perms_string'] ?>
                                    </span>
                                <?php elseif ($item['readable']): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-yellow-600/20 text-yellow-400 border border-yellow-500/30">
                                        <?= $item['perms_string'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-red-600/20 text-red-400 border border-red-500/30">
                                        <?= $item['perms_string'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-sm hidden md:table-cell" onclick="selectItem(event, this.closest('tr'))">
                            <?= date('M d, Y H:i', $item['modified']) ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="showContextMenu(event, this.closest('tr'))" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <div id="contextMenu" class="context-menu glass border border-slate-700/50 rounded-xl shadow-2xl py-2 min-w-56 overflow-y-auto" style="max-height: calc(100vh - 40px);">
        <button onclick="openItem()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>📂</span> Open
        </button>
        <button id="ctxEdit" onclick="editFile()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>✏️</span> Edit
        </button>
        <button id="ctxPreview" onclick="previewFile()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>👁️</span> Preview
        </button>
        <button onclick="showPermissionsModal()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>🔐</span> Check Permissions
        </button>
        <button onclick="downloadFile()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>⬇️</span> Download
        </button>
        <button onclick="copyDirectUrl()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>🔗</span> Copy URL
        </button>
        <div class="border-t border-slate-700/50 my-2"></div>
        <button onclick="showRenameModal()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>📝</span> Rename
        </button>
        <button onclick="showCopyModal()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>📋</span> Copy to...
        </button>
        <button onclick="showMoveModal()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>📦</span> Move to...
        </button>
        <div class="border-t border-slate-700/50 my-2"></div>
        <button id="ctxCompress" onclick="showCompressModal()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>📦</span> Compress
        </button>
        <button id="ctxExtract" onclick="extractArchive()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>📂</span> Extract
        </button>
        <button onclick="createBackup()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>💾</span> Backup
        </button>
        <button id="ctxShare" onclick="showShareModal()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700/50 text-left transition-colors">
            <span>🔗</span> Share Link
        </button>
        <button id="ctxScan" onclick="scanSingleFile()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-orange-600/20 text-orange-400 text-left transition-colors">
            <span>🛡️</span> Scan for Malware
        </button>
        <div class="border-t border-slate-700/50 my-2"></div>
        <button onclick="deleteItem()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-600/20 text-red-400 text-left transition-colors">
            <span>🗑️</span> Delete
        </button>
    </div>
    <div id="uploadModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-lg border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">⬆️</span> Upload Files
            </h3>
            <div class="flex gap-2 mb-6">
                <button onclick="showUploadTab('files')" id="tabFiles" class="flex-1 py-2 px-4 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 transition-all">
                    📁 Files
                </button>
                <button onclick="showUploadTab('url')" id="tabUrl" class="flex-1 py-2 px-4 rounded-xl bg-slate-700/30 text-slate-400 border border-slate-600/30 transition-all hover:bg-slate-700/50">
                    🔗 From URL
                </button>
            </div>
            <div id="uploadFilesTab">
                <div id="uploadDropZone" class="border-2 border-dashed border-slate-600/50 rounded-2xl p-8 text-center cursor-pointer hover:border-indigo-500/50 transition-all">
                    <div class="text-5xl mb-3">📤</div>
                    <p class="text-slate-300 font-medium">Drag & drop files here</p>
                    <p class="text-slate-500 text-sm mt-1">or click to browse</p>
                    <input type="file" id="fileInput" multiple class="hidden">
                </div>
                <div id="uploadList" class="max-h-40 overflow-y-auto mt-4 space-y-2"></div>
            </div>
            <div id="uploadUrlTab" class="hidden">
                <input type="url" id="uploadUrlInput" placeholder="https://example.com/file.zip"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <p class="text-slate-500 text-xs mt-2">Enter a direct URL to download the file</p>
            </div>
            <div id="uploadProgress" class="hidden mt-4">
                <div class="bg-slate-700/50 rounded-full h-2 overflow-hidden">
                    <div id="progressBar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('uploadModal')" class="btn-action btn-secondary">Cancel</button>
                <button id="uploadBtn" onclick="uploadFiles()" class="btn-action btn-primary" disabled>
                    <span>⬆️</span> Upload
                </button>
            </div>
        </div>
    </div>
    <div id="createFolderModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📁</span> Create Folder
            </h3>
            <input type="text" id="newFolderName" placeholder="Folder name" 
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('createFolderModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="createFolder()" class="btn-action btn-primary">Create</button>
            </div>
        </div>
    </div>
    <div id="createFileModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📄</span> Create File
            </h3>
            <input type="text" id="newFileName" placeholder="filename.txt" 
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('createFileModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="createFile()" class="btn-action btn-primary">Create</button>
            </div>
        </div>
    </div>
    <div id="renameModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📝</span> Rename
            </h3>
            <input type="hidden" id="renameOldName">
            <input type="text" id="renameNewName" placeholder="New name" 
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('renameModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="renameItem()" class="btn-action btn-primary">Rename</button>
            </div>
        </div>
    </div>
    <div id="moveModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📦</span> Move To
            </h3>
            <input type="hidden" id="moveFileName">
            <select id="moveDestination" class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <option value="">📁 Root</option>
            </select>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('moveModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="moveItem()" class="btn-action btn-primary">Move</button>
            </div>
        </div>
    </div>
    <div id="copyModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📋</span> Copy To
            </h3>
            <input type="hidden" id="copyFileName">
            <select id="copyDestination" class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <option value="">📁 Root</option>
            </select>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('copyModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="copyItem()" class="btn-action btn-primary">Copy</button>
            </div>
        </div>
    </div>
    <div id="bulkMoveModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📦</span> Move Items
            </h3>
            <p id="bulkMoveCount" class="text-slate-400 mb-4">0 item(s) selected</p>
            <select id="bulkMoveDestination" class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <option value="">📁 Root</option>
            </select>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('bulkMoveModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="bulkMove()" class="btn-action btn-primary">Move All</button>
            </div>
        </div>
    </div>
    <div id="bulkCopyModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📋</span> Copy Items
            </h3>
            <p id="bulkCopyCount" class="text-slate-400 mb-4">0 item(s) selected</p>
            <select id="bulkCopyDestination" class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                <option value="">📁 Root</option>
            </select>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('bulkCopyModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="bulkCopy()" class="btn-action btn-primary">Copy All</button>
            </div>
        </div>
    </div>
    <div id="compressModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">📦</span> Compress
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Format</label>
                    <select id="compressFormat" class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="zip">ZIP (.zip)</option>
                        <option value="tar.gz">TAR.GZ (.tar.gz)</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('compressModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="compressItem()" class="btn-action btn-primary">Compress</button>
            </div>
        </div>
    </div>
    <div id="bulkCompressModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">🗜️</span> Compress Items
            </h3>
            <p id="bulkCompressCount" class="text-slate-400 mb-4">0 item(s) selected</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Archive Name</label>
                    <input type="text" id="bulkCompressName" placeholder="archive" 
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Format</label>
                    <select id="bulkCompressFormat" class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="zip">ZIP (.zip)</option>
                        <option value="tar.gz">TAR.GZ (.tar.gz)</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('bulkCompressModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="bulkCompress()" class="btn-action btn-primary">Compress All</button>
            </div>
        </div>
    </div>
    <div id="editorModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="editor-panel glass rounded-2xl p-6 w-full max-w-6xl max-h-[90vh] border border-slate-700/50 shadow-2xl flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">✏️</span> <span id="editorFileName">file.txt</span>
                </h3>
                <div class="flex items-center gap-3">
                    <select id="editorTheme" onchange="changeEditorTheme(this.value)" class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-sm">
                        <option value="monokai">Monokai</option>
                        <option value="github">GitHub</option>
                        <option value="tomorrow_night">Tomorrow Night</option>
                        <option value="twilight">Twilight</option>
                        <option value="cobalt">Cobalt</option>
                        <option value="dracula">Dracula</option>
                        <option value="nord_dark">Nord Dark</option>
                        <option value="one_dark">One Dark</option>
                        <option value="solarized_dark">Solarized Dark</option>
                        <option value="solarized_light">Solarized Light</option>
                        <option value="terminal">Terminal</option>
                        <option value="vibrant_ink">Vibrant Ink</option>
                    </select>
                    <button onclick="toggleEditorFullscreen()" id="fullscreenBtn" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors" title="Toggle Fullscreen (F11)">
                        <svg id="fullscreenIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </button>
                    <button onclick="hideModal('editorModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="editor" class="flex-1 min-h-0"></div>
            <div class="flex gap-3 justify-end mt-4">
                <button onclick="hideModal('editorModal')" class="btn-action btn-secondary">Close</button>
                <button onclick="saveFile()" class="btn-action btn-primary">
                    <span>💾</span> Save (Ctrl+S)
                </button>
            </div>
        </div>
    </div>
    <div id="previewModal" class="modal fixed inset-0 bg-black/80 backdrop-blur-sm items-center justify-center z-50 p-4" onclick="hideModal('previewModal')">
        <div id="previewContent" class="max-w-full max-h-full" onclick="event.stopPropagation()"></div>
    </div>
    <div id="searchModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-2xl max-h-[80vh] border border-slate-700/50 shadow-2xl flex flex-col">
            <h3 class="text-xl font-bold mb-4 flex items-center gap-3">
                <span class="text-2xl">🔍</span> Search Results
            </h3>
            <div id="searchResults" class="flex-1 overflow-y-auto space-y-2"></div>
            <div class="flex gap-3 justify-end mt-4">
                <button onclick="hideModal('searchModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    <div id="shareModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">🔗</span> Share File
            </h3>
            <p class="text-slate-400 mb-4">File: <span id="shareFileName" class="text-white font-medium"></span></p>
            <div id="shareOptions">
                <label class="block text-sm text-slate-400 mb-2">Expiry</label>
                <select id="shareExpiry" class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all mb-4">
                    <?php foreach ($CONFIG['share_expiry_options'] as $value => $label): ?>
                    <option value="<?= $value ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <button onclick="createShareLink()" class="w-full btn-action btn-primary justify-center">
                    <span>✨</span> Generate Share Link
                </button>
            </div>
            <div id="shareResult" class="hidden">
                <label class="block text-sm text-slate-400 mb-2">Share URL</label>
                <div class="flex gap-2">
                    <input type="text" id="shareUrlInput" readonly 
                        class="flex-1 px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm">
                    <button onclick="copyShareUrl()" class="btn-action btn-primary">
                        <span>📋</span> Copy
                    </button>
                </div>
                <p id="shareExpiryInfo" class="text-slate-500 text-xs mt-2"></p>
                <button onclick="resetShareModal()" class="w-full mt-4 btn-action btn-secondary justify-center">
                    Create Another Link
                </button>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('shareModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    <div id="manageSharesModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-2xl max-h-[80vh] border border-slate-700/50 shadow-2xl flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">🔗</span> Manage Shares
                </h3>
                <button onclick="cleanupExpiredShares()" class="btn-action btn-secondary text-sm">
                    <span>🧹</span> Cleanup Expired
                </button>
            </div>
            <div id="sharesList" class="flex-1 overflow-y-auto space-y-3">
                <p class="text-slate-500 text-center py-8">Loading shares...</p>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('manageSharesModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    <div id="scanModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-3xl max-h-[85vh] border border-slate-700/50 shadow-2xl flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">🛡️</span> Security Scan Results
                </h3>
                <button onclick="hideModal('scanModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="scanLoading" class="text-center py-12">
                <div class="inline-block w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-slate-400">Scanning files for potential threats...</p>
            </div>
            <div id="scanSummary" class="hidden mb-6">
                <div class="grid grid-cols-4 gap-4">
                    <div class="glass rounded-xl p-4 text-center border border-slate-700/50">
                        <p class="text-2xl font-bold text-white" id="scanScanned">0</p>
                        <p class="text-xs text-slate-400">Scanned</p>
                    </div>
                    <div class="glass rounded-xl p-4 text-center border border-red-500/30 bg-red-900/10">
                        <p class="text-2xl font-bold text-red-400" id="scanCritical">0</p>
                        <p class="text-xs text-red-400">Critical</p>
                    </div>
                    <div class="glass rounded-xl p-4 text-center border border-orange-500/30 bg-orange-900/10">
                        <p class="text-2xl font-bold text-orange-400" id="scanHigh">0</p>
                        <p class="text-xs text-orange-400">High</p>
                    </div>
                    <div class="glass rounded-xl p-4 text-center border border-yellow-500/30 bg-yellow-900/10">
                        <p class="text-2xl font-bold text-yellow-400" id="scanMedium">0</p>
                        <p class="text-xs text-yellow-400">Medium</p>
                    </div>
                </div>
            </div>
            <div id="scanClean" class="hidden items-center justify-center gap-4 py-12 text-center">
                <div class="text-6xl mb-4">✅</div>
                <h4 class="text-xl font-bold text-green-400">All Clear!</h4>
                <p class="text-slate-400">No suspicious patterns detected in scanned files.</p>
            </div>
            <div id="scanResults" class="flex-1 overflow-y-auto space-y-4 hidden"></div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('scanModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    <div id="shortcutsModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                <span class="text-2xl">⌨️</span> Keyboard Shortcuts
            </h3>
            <div class="space-y-1 text-sm max-h-[60vh] overflow-y-auto pr-2">
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-2 mt-2">Navigation</p>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Go Back / Parent Folder</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Backspace</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Open Item</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Enter</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Refresh</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">F5</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Search Files</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + F</kbd>
                </div>
                
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-2 mt-4">Selection</p>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Select All</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + A</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Clear Selection</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Escape</kbd>
                </div>
                
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-2 mt-4">File Operations</p>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">New File</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + N</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">New Folder</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + Shift + N</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Rename</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">F2</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Delete Selected</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Delete</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Download</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + D</kbd>
                </div>
                
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-2 mt-4">Clipboard</p>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Copy</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + C</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Cut</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + X</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Paste</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + V</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Undo</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + Z</kbd>
                </div>
                
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-2 mt-4">Bulk Operations</p>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Move Selected</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + Shift + M</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Copy to Destination</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + Shift + C</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Compress Selected</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + Shift + Z</kbd>
                </div>
                
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-2 mt-4">Editor</p>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Save File</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + S</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Toggle Fullscreen</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">F11</kbd>
                </div>
                
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-2 mt-4">Other</p>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">File Properties</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + I</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-700/30">
                    <span class="text-slate-400">Upload Files</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">Ctrl + U</kbd>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-slate-400">Show Shortcuts Help</span>
                    <kbd class="px-2 py-1 bg-slate-700/50 rounded text-xs">?</kbd>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('shortcutsModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    <!-- Permissions Modal -->
    <div id="permissionsModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto border border-slate-700/50 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">🔐</span> File Permissions
                </h3>
                <button onclick="hideModal('permissionsModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="permissionsLoading" class="text-center py-8">
                <div class="inline-block w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-slate-400">Loading permissions...</p>
            </div>
            <div id="permissionsContent" class="hidden space-y-4">
                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                    <div class="flex items-center gap-3 mb-3">
                        <span id="permFileName" class="text-lg font-medium truncate"></span>
                    </div>
                    <div class="text-sm text-slate-400" id="permFilePath"></div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div id="permReadBox" class="text-center p-4 rounded-xl border">
                        <div class="text-2xl mb-1" id="permReadIcon">📖</div>
                        <div class="font-medium" id="permReadText">Read</div>
                        <div class="text-xs text-slate-400" id="permReadStatus">—</div>
                    </div>
                    <div id="permWriteBox" class="text-center p-4 rounded-xl border">
                        <div class="text-2xl mb-1" id="permWriteIcon">✏️</div>
                        <div class="font-medium" id="permWriteText">Write</div>
                        <div class="text-xs text-slate-400" id="permWriteStatus">—</div>
                    </div>
                    <div id="permExecBox" class="text-center p-4 rounded-xl border">
                        <div class="text-2xl mb-1" id="permExecIcon">⚙️</div>
                        <div class="font-medium" id="permExecText">Execute</div>
                        <div class="text-xs text-slate-400" id="permExecStatus">—</div>
                    </div>
                </div>
                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Permissions</span>
                        <span class="font-mono" id="permOctal">0755</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Mode String</span>
                        <span class="font-mono text-indigo-400" id="permString">rwxr-xr-x</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Owner</span>
                        <span id="permOwner">—</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Group</span>
                        <span id="permGroup">—</span>
                    </div>
                </div>
                
                <!-- Ownership Details Section -->
                <div id="ownershipSection" class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">👤</span>
                        <h4 class="font-medium">Ownership Details</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">File Owner</span>
                            <span id="ownerDetail" class="font-mono">—</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">File Group</span>
                            <span id="groupDetail" class="font-mono">—</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Web Server User</span>
                            <span id="webUserDetail" class="font-mono text-indigo-400">—</span>
                        </div>
                        <div id="ownershipStatus" class="mt-3 pt-3 border-t border-slate-700/50">
                            <!-- Filled by JS -->
                        </div>
                    </div>
                </div>
                
                <!-- SSH Commands Section -->
                <div id="sshCommandsSection" class="hidden bg-amber-900/20 border border-amber-500/30 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">💻</span>
                        <div>
                            <h4 class="font-medium text-amber-400">Fix Ownership via SSH</h4>
                            <p class="text-xs text-slate-400">Run these commands in your server terminal</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-slate-900/50 rounded-lg p-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-slate-400">Single item</span>
                                <button onclick="copyCommand('chownSingle')" class="text-xs px-2 py-0.5 bg-slate-700/50 hover:bg-slate-600/50 rounded transition-colors">📋 Copy</button>
                            </div>
                            <code id="chownSingle" class="text-xs font-mono text-amber-300 block overflow-x-auto whitespace-nowrap">chown user:group file</code>
                        </div>
                        <div id="chownRecursiveBox" class="bg-slate-900/50 rounded-lg p-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-slate-400">Recursive (folder + contents)</span>
                                <button onclick="copyCommand('chownRecursive')" class="text-xs px-2 py-0.5 bg-slate-700/50 hover:bg-slate-600/50 rounded transition-colors">📋 Copy</button>
                            </div>
                            <code id="chownRecursive" class="text-xs font-mono text-amber-300 block overflow-x-auto whitespace-nowrap">chown -R user:group folder</code>
                        </div>
                        <div class="text-xs text-slate-400 mt-2">
                            <span class="text-amber-400">💡 Tip:</span> If you don't have SSH access, use cPanel's File Manager to change ownership, or contact your hosting provider.
                        </div>
                    </div>
                </div>
                <div id="permOperations" class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                    <h4 class="font-medium mb-3">Allowed Operations</h4>
                    <div class="flex flex-wrap gap-2" id="permOpsList">
                        <!-- Filled by JS -->
                    </div>
                </div>
                <div id="permWarning" class="hidden bg-yellow-900/20 border border-yellow-500/30 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-xl">⚠️</span>
                        <div>
                            <p class="font-medium text-yellow-400">Limited Access</p>
                            <p class="text-sm text-slate-400 mt-1" id="permWarningText"></p>
                        </div>
                    </div>
                </div>
                
                <?php if (isAdmin()): ?>
                <!-- chmod Section -->
                <div id="chmodSection" class="bg-indigo-900/20 border border-indigo-500/30 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xl">⚡</span>
                        <div>
                            <p class="font-medium text-indigo-400">Change Permissions</p>
                            <p class="text-xs text-slate-400">Attempt to chmod (when server allows)</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" id="chmodInput" placeholder="755" maxlength="4"
                               class="flex-1 bg-slate-800/50 border border-slate-600/50 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30">
                        <button onclick="attemptChmod()" id="chmodBtn" 
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                            <span>⚡</span> Apply
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mt-3">
                        <button onclick="setChmodPreset('755')" class="text-xs px-2 py-1 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg transition-colors">755 (Folder)</button>
                        <button onclick="setChmodPreset('644')" class="text-xs px-2 py-1 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg transition-colors">644 (File)</button>
                        <button onclick="setChmodPreset('777')" class="text-xs px-2 py-1 bg-red-700/50 hover:bg-red-600/50 rounded-lg transition-colors">777 (Full)</button>
                    </div>
                    
                    <!-- Recursive chmod option (shown only for directories) -->
                    <div id="chmodRecursiveSection" class="hidden mt-3 p-3 bg-slate-800/30 rounded-lg border border-slate-600/30">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="chmodRecursive" class="mt-0.5 w-4 h-4 rounded border-slate-500 text-indigo-600 focus:ring-indigo-500 bg-slate-700">
                            <div>
                                <span class="text-sm font-medium text-slate-200">Apply recursively</span>
                                <p class="text-xs text-slate-400 mt-0.5">Change permissions on all files and subfolders</p>
                            </div>
                        </label>
                        <div id="chmodFileModeSection" class="hidden mt-3 pl-7">
                            <label class="block text-xs text-slate-400 mb-1">File mode (optional, defaults to removing execute bit)</label>
                            <input type="text" id="chmodFileMode" placeholder="644" maxlength="4"
                                   class="w-24 bg-slate-700/50 border border-slate-600/50 rounded-lg px-2 py-1 text-sm font-mono focus:outline-none focus:border-indigo-500/50">
                        </div>
                    </div>
                    
                    <!-- Progress indicator for recursive operations -->
                    <div id="chmodProgress" class="hidden mt-3 p-3 bg-slate-800/30 rounded-lg border border-slate-600/30">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-300">Processing...</span>
                            <span id="chmodProgressPercent" class="text-sm font-mono text-indigo-400">0%</span>
                        </div>
                        <div class="w-full bg-slate-700/50 rounded-full h-2 overflow-hidden">
                            <div id="chmodProgressBar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-150" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between mt-2 text-xs text-slate-400">
                            <span id="chmodProgressCurrent" class="truncate max-w-[60%]">Starting...</span>
                            <span id="chmodProgressStats">0 / 0</span>
                        </div>
                        <div class="flex gap-4 mt-2 text-xs">
                            <span class="text-green-400"><span id="chmodSuccessCount">0</span> ✓</span>
                            <span class="text-red-400"><span id="chmodFailedCount">0</span> ✗</span>
                        </div>
                    </div>
                    
                    <div id="chmodResult" class="hidden mt-3 p-3 rounded-lg text-sm"></div>
                </div>
                <?php endif; ?>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('permissionsModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    <?php if (isAdmin()): ?>
    <div id="settingsModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-2xl max-h-[85vh] border border-slate-700/50 shadow-2xl flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">⚙️</span> User Settings
                </h3>
                <button onclick="hideModal('settingsModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex gap-2 mb-6 border-b border-slate-700/50 pb-4">
                <button id="tabUsers" onclick="showSettingsTab('users')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">
                    👥 Users
                </button>
                <button id="tabAddUser" onclick="showSettingsTab('addUser')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all bg-slate-700/30 text-slate-400 border border-slate-600/30">
                    ➕ Add User
                </button>
            </div>
            <div id="settingsUsersTab" class="flex-1 overflow-y-auto">
                <div id="usersList" class="space-y-3">
                    <p class="text-slate-500 text-center py-8">Loading users...</p>
                </div>
            </div>
            <div id="settingsAddUserTab" class="hidden flex-1 overflow-y-auto">
                <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
                    <h4 class="font-semibold mb-4 flex items-center gap-2">
                        <span>👤</span> Create New User
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Username</label>
                            <input type="text" id="newUserUsername" placeholder="username" 
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="32">
                            <p class="text-xs text-slate-500 mt-1">3-32 characters, letters, numbers and underscores only</p>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Password</label>
                            <input type="password" id="newUserPassword" placeholder="••••••••" 
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                minlength="6">
                            <p class="text-xs text-slate-500 mt-1">Minimum 6 characters</p>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Root Directory</label>
                            <input type="text" id="newUserRoot" placeholder="/var/www/html" value="<?= __DIR__ ?>/uploads"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <p class="text-xs text-slate-500 mt-1">Absolute path to user's accessible directory</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" id="newUserIsAdmin" 
                                    class="w-5 h-5 rounded border-slate-600 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm">Grant Admin Privileges</span>
                            </label>
                        </div>
                        <div class="pt-4">
                            <button onclick="createNewUser()" class="w-full btn-action btn-primary justify-center">
                                <span>➕</span> Create User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-700/50 flex justify-end">
                <button onclick="hideModal('settingsModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    <div id="editUserModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">✏️</span> Edit User
                </h3>
                <button onclick="hideModal('editUserModal')" class="text-slate-400 hover:text-white transition-colors">✕</button>
            </div>
            <input type="hidden" id="editUserUsername">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Username</label>
                    <input type="text" id="editUserDisplayName" disabled
                        class="w-full px-4 py-3 bg-slate-800/30 border border-slate-700/50 rounded-xl text-slate-400">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-2">New Password (leave blank to keep current)</label>
                    <input type="password" id="editUserPassword" placeholder="••••••••" 
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Root Directory</label>
                    <input type="text" id="editUserRoot" placeholder="/var/www/html"
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="editUserIsAdmin" 
                            class="w-5 h-5 rounded border-slate-600 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm">Admin Privileges</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button onclick="hideModal('editUserModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="saveUserChanges()" class="btn-action btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Security Dashboard Modal -->
    <div id="securityDashboardModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-3xl max-h-[85vh] border border-slate-700/50 shadow-2xl flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">🔐</span> Security Dashboard
                </h3>
                <button onclick="hideModal('securityDashboardModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4">
                <div id="securityStatus" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl" id="statusHttps">⏳</span>
                            <span class="text-sm text-slate-400">HTTPS</span>
                        </div>
                        <p class="text-lg font-semibold" id="httpsStatus">Checking...</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">🛡️</span>
                            <span class="text-sm text-slate-400">Rate Limiting</span>
                        </div>
                        <p class="text-lg font-semibold text-green-400">Active</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">🔏</span>
                            <span class="text-sm text-slate-400">Session Fingerprinting</span>
                        </div>
                        <p class="text-lg font-semibold text-green-400">Active</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">📋</span>
                            <span class="text-sm text-slate-400">Audit Logging</span>
                        </div>
                        <p class="text-lg font-semibold" id="auditStatus">Checking...</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">🚫</span>
                            <span class="text-sm text-slate-400">Blocked IPs</span>
                        </div>
                        <p class="text-lg font-semibold" id="blockedIpsCount">0</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">⏱️</span>
                            <span class="text-sm text-slate-400">Session Timeout</span>
                        </div>
                        <p class="text-lg font-semibold" id="sessionTimeout">...</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/20 rounded-xl p-4">
                    <h4 class="font-semibold text-green-400 mb-3 flex items-center gap-2">
                        <span>✅</span> Security Features Enabled
                    </h4>
                    <ul class="text-sm text-slate-300 space-y-2">
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> HTTP Security Headers (X-Frame-Options, CSP, etc.)</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Rate limiting for login attempts (5 attempts / 15 min lockout)</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Session fingerprinting to prevent hijacking</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> CSRF protection on all forms</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> MIME type verification for uploads</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Path traversal protection</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Comprehensive audit logging</li>
                    </ul>
                </div>
                
                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                    <h4 class="font-semibold mb-3 flex items-center gap-2">
                        <span>🚫</span> Currently Blocked IPs
                    </h4>
                    <div id="blockedIpsList" class="space-y-2">
                        <p class="text-slate-500 text-sm">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-700/50 flex justify-end">
                <button onclick="hideModal('securityDashboardModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    
    <!-- Audit Log Modal -->
    <div id="auditLogModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-4xl max-h-[85vh] border border-slate-700/50 shadow-2xl flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">📋</span> Audit Log
                </h3>
                <button onclick="hideModal('auditLogModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex gap-2 mb-4">
                <select id="auditLogLines" class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="50">Last 50 lines</option>
                    <option value="100" selected>Last 100 lines</option>
                    <option value="200">Last 200 lines</option>
                    <option value="500">Last 500 lines</option>
                </select>
                <button onclick="loadAuditLog()" class="btn-action btn-secondary">
                    <span>🔄</span> Refresh
                </button>
                <button onclick="clearAuditLog()" class="btn-action btn-danger">
                    <span>🗑️</span> Clear Log
                </button>
            </div>
            <div id="auditLogContent" class="flex-1 overflow-auto bg-slate-900/50 rounded-xl p-4 font-mono text-xs text-slate-300 whitespace-pre-wrap">
                Loading audit log...
            </div>
            <div class="mt-6 pt-4 border-t border-slate-700/50 flex justify-end">
                <button onclick="hideModal('auditLogModal')" class="btn-action btn-secondary">Close</button>
            </div>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-md border border-slate-700/50 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">🔑</span> Change Password
                </h3>
                <button onclick="hideModal('changePasswordModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Current Password</label>
                    <input type="password" id="currentPassword" placeholder="Enter current password"
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-2">New Password</label>
                    <input type="password" id="newPassword" placeholder="Enter new password"
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    <p class="text-xs text-slate-500 mt-1">Min 8 chars, must include uppercase, lowercase, and number</p>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Confirm New Password</label>
                    <input type="password" id="confirmPassword" placeholder="Confirm new password"
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="hideModal('changePasswordModal')" class="btn-action btn-secondary">Cancel</button>
                <button onclick="changePassword()" class="btn-action btn-primary">Change Password</button>
            </div>
        </div>
    </div>
    <div id="phpmyadminModal" class="modal fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="glass rounded-2xl p-6 w-full max-w-lg border border-slate-700/50 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="text-2xl">🐬</span> phpMyAdmin Manager
                </h3>
                <button onclick="hideModal('phpmyadminModal')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="pmaStatus" class="mb-6">
                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                    <div class="flex items-center gap-3 mb-3">
                        <div id="pmaStatusIcon" class="text-3xl">🔍</div>
                        <div>
                            <h4 id="pmaStatusTitle" class="font-semibold text-white">Checking status...</h4>
                            <p id="pmaStatusDesc" class="text-sm text-slate-400">Please wait</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-orange-500/10 to-yellow-500/10 border border-orange-500/20 rounded-xl p-4 mb-6">
                <h4 class="font-semibold text-orange-400 mb-2 flex items-center gap-2">
                    <span>ℹ️</span> About phpMyAdmin
                </h4>
                <p class="text-sm text-slate-300">
                    phpMyAdmin is a free, open-source tool for managing MySQL and MariaDB databases through a web interface.
                </p>
                <ul class="text-xs text-slate-400 mt-2 space-y-1">
                    <li>• Browse, edit, and manage databases</li>
                    <li>• Execute SQL queries</li>
                    <li>• Import/export data</li>
                    <li>• Manage users and permissions</li>
                </ul>
            </div>
            <div id="pmaProgress" class="hidden mb-6">
                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="animate-spin w-8 h-8 border-4 border-orange-500 border-t-transparent rounded-full"></div>
                        <div>
                            <h4 id="pmaProgressTitle" class="font-semibold text-white">Installing phpMyAdmin...</h4>
                            <p id="pmaProgressDesc" class="text-sm text-slate-400">Downloading files, please wait</p>
                        </div>
                    </div>
                    <div class="w-full bg-slate-700/50 rounded-full h-2 overflow-hidden">
                        <div id="pmaProgressBar" class="bg-gradient-to-r from-orange-500 to-yellow-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div id="pmaActions" class="flex gap-3">
                <button id="pmaInstallBtn" onclick="installPhpMyAdmin()" class="flex-1 btn-action btn-primary justify-center hidden">
                    <span>⬇️</span> Install phpMyAdmin
                </button>
                <button id="pmaOpenBtn" onclick="openPhpMyAdmin()" class="flex-1 btn-action btn-primary justify-center hidden" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.3), rgba(16, 185, 129, 0.3)); border-color: rgba(34, 197, 94, 0.5);">
                    <span>🚀</span> Open phpMyAdmin
                </button>
                <button id="pmaUninstallBtn" onclick="uninstallPhpMyAdmin()" class="btn-action btn-danger justify-center hidden">
                    <span>🗑️</span> Uninstall
                </button>
            </div>
            <div class="text-center text-xs text-slate-500 mt-4 pt-4 border-t border-slate-700/50">
                Version 5.2.3 • <a href="https://www.phpmyadmin.net/" target="_blank" rel="noopener noreferrer" class="text-orange-400 hover:text-orange-300">Official Website</a>
            </div>
        </div>
    </div>
    <div id="toast" class="toast fixed bottom-6 right-6 glass border border-slate-700/50 rounded-xl shadow-2xl px-6 py-4 flex items-center gap-3 z-50">
        <span id="toastIcon" class="text-xl">✅</span>
        <p id="toastMessage" class="font-medium"></p>
    </div>
    <script>
        const csrfToken = '<?= $_SESSION['csrf_token'] ?>';
        const currentPath = '<?= $currentPath ?>';
        const baseUrl = '<?= $CONFIG['base_url'] ?>';
        const maxPreviewSize = <?= $CONFIG['max_preview_size'] ?>;
        let selectedItem = null;
        let uploadQueue = [];
        let currentView = localStorage.getItem('fm_view') || 'grid';
        let editor = null;
        let currentEditingFile = null;
        document.addEventListener('DOMContentLoaded', () => {
            setView(currentView);
            initDragDrop();
            initEditor();
            loadFolderTree();
        });
        async function toggleHiddenFiles() {
            const formData = new FormData();
            formData.append('action', 'toggle_hidden');
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(result.show_hidden ? 'Showing hidden files' : 'Hiding hidden files');
                    location.reload();
                } else {
                    showToast('Failed to toggle hidden files', 'error');
                }
            } catch (e) {
                showToast('Error toggling hidden files', 'error');
            }
        }
        function showSettingsModal() {
            showModal('settingsModal');
            loadUsers();
        }
        function showSettingsTab(tab) {
            const usersTab = document.getElementById('settingsUsersTab');
            const addUserTab = document.getElementById('settingsAddUserTab');
            const tabUsersBtn = document.getElementById('tabUsers');
            const tabAddUserBtn = document.getElementById('tabAddUser');
            if (tab === 'users') {
                usersTab.classList.remove('hidden');
                addUserTab.classList.add('hidden');
                tabUsersBtn.classList.add('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabUsersBtn.classList.remove('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
                tabAddUserBtn.classList.remove('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabAddUserBtn.classList.add('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
                loadUsers();
            } else {
                usersTab.classList.add('hidden');
                addUserTab.classList.remove('hidden');
                tabAddUserBtn.classList.add('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabAddUserBtn.classList.remove('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
                tabUsersBtn.classList.remove('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabUsersBtn.classList.add('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
            }
        }
        async function loadUsers() {
            const container = document.getElementById('usersList');
            container.innerHTML = '<p class="text-slate-500 text-center py-8">Loading users...</p>';
            const formData = new FormData();
            formData.append('action', 'get_users');
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success && result.users.length > 0) {
                    container.innerHTML = result.users.map(user => {
                        const isCurrentUser = user.username === '<?= $_SESSION['username'] ?? '' ?>';
                        const sourceLabel = user.source === 'default' ? 'Default' : 
                                           user.source === 'modified' ? 'Modified' : 'Custom';
                        const sourceColor = user.source === 'default' ? 'text-slate-500' : 
                                           user.source === 'modified' ? 'text-yellow-400' : 'text-green-400';
                        return `
                            <div class="glass rounded-xl p-4 border border-slate-700/50 ${isCurrentUser ? 'border-indigo-500/30 bg-indigo-600/5' : ''}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                            ${user.username.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">${user.username}</span>
                                                ${user.is_admin ? '<span class="px-2 py-0.5 bg-indigo-600/30 text-indigo-400 text-xs rounded-full">Admin</span>' : ''}
                                                ${isCurrentUser ? '<span class="px-2 py-0.5 bg-green-600/30 text-green-400 text-xs rounded-full">You</span>' : ''}
                                            </div>
                                            <p class="text-xs text-slate-500 truncate max-w-xs">${user.root}</p>
                                            <span class="text-xs ${sourceColor}">${sourceLabel}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="editUser('${user.username}', '${user.root.replace(/'/g, "\\'")}', ${user.is_admin})" 
                                            class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors" title="Edit">✏️</button>
                                        ${!isCurrentUser ? `<button onclick="deleteUserConfirm('${user.username}')" 
                                            class="p-2 hover:bg-red-600/20 text-red-400 rounded-lg transition-colors" title="Delete">🗑️</button>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = '<p class="text-slate-500 text-center py-8">No users found</p>';
                }
            } catch (e) {
                container.innerHTML = '<p class="text-red-400 text-center py-8">Failed to load users</p>';
            }
        }
        async function createNewUser() {
            const username = document.getElementById('newUserUsername').value.trim();
            const password = document.getElementById('newUserPassword').value;
            const root = document.getElementById('newUserRoot').value.trim();
            const isAdmin = document.getElementById('newUserIsAdmin').checked;
            if (!username || username.length < 3) {
                showToast('Username must be at least 3 characters', 'error');
                return;
            }
            if (!password || password.length < 6) {
                showToast('Password must be at least 6 characters', 'error');
                return;
            }
            if (!root) {
                showToast('Root directory is required', 'error');
                return;
            }
            const formData = new FormData();
            formData.append('action', 'create_user');
            formData.append('username', username);
            formData.append('password', password);
            formData.append('root', root);
            formData.append('is_admin', isAdmin ? '1' : '0');
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message);
                    document.getElementById('newUserUsername').value = '';
                    document.getElementById('newUserPassword').value = '';
                    document.getElementById('newUserIsAdmin').checked = false;
                    showSettingsTab('users');
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Failed to create user', 'error');
            }
        }
        function editUser(username, root, isAdmin) {
            document.getElementById('editUserUsername').value = username;
            document.getElementById('editUserDisplayName').value = username;
            document.getElementById('editUserPassword').value = '';
            document.getElementById('editUserRoot').value = root;
            document.getElementById('editUserIsAdmin').checked = isAdmin;
            showModal('editUserModal');
        }
        async function saveUserChanges() {
            const username = document.getElementById('editUserUsername').value;
            const password = document.getElementById('editUserPassword').value;
            const root = document.getElementById('editUserRoot').value.trim();
            const isAdmin = document.getElementById('editUserIsAdmin').checked;
            if (!root) {
                showToast('Root directory is required', 'error');
                return;
            }
            if (password && password.length < 6) {
                showToast('Password must be at least 6 characters', 'error');
                return;
            }
            const formData = new FormData();
            formData.append('action', 'update_user');
            formData.append('username', username);
            formData.append('password', password);
            formData.append('root', root);
            formData.append('is_admin', isAdmin ? '1' : '0');
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message);
                    hideModal('editUserModal');
                    loadUsers();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Failed to update user', 'error');
            }
        }
        async function deleteUserConfirm(username) {
            if (!confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`)) {
                return;
            }
            const formData = new FormData();
            formData.append('action', 'delete_user');
            formData.append('username', username);
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message);
                    loadUsers();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Failed to delete user', 'error');
            }
        }
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        }
        document.getElementById('menuBtn').addEventListener('click', toggleSidebar);
        function initEditor() {
            editor = ace.edit('editor');
            editor.setTheme('ace/theme/<?= $CONFIG['editor_theme'] ?>');
            editor.session.setMode('ace/mode/text');
            editor.setOptions({
                fontSize: '<?= $CONFIG['editor_font_size'] ?>px',
                showPrintMargin: false,
                wrap: true,
                enableBasicAutocompletion: true,
                enableLiveAutocompletion: true,
            });
            editor.commands.addCommand({
                name: 'save',
                bindKey: { win: 'Ctrl-S', mac: 'Cmd-S' },
                exec: saveFile
            });
        }
        function changeEditorTheme(theme) {
            editor.setTheme('ace/theme/' + theme);
            localStorage.setItem('fm_editor_theme', theme);
        }
        function toggleEditorFullscreen() {
            const modal = document.getElementById('editorModal');
            const icon = document.getElementById('fullscreenIcon');
            modal.classList.toggle('fullscreen');
            const isFullscreen = modal.classList.contains('fullscreen');
            if (isFullscreen) {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>';
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>';
            }
            setTimeout(() => {
                editor.resize();
                editor.renderer.updateFull();
            }, 50);
        }
        function initDragDrop() {
            const mainContent = document.querySelector('main');
            const dropZone = document.getElementById('dropZone');
            ['dragenter', 'dragover'].forEach(event => {
                mainContent.addEventListener(event, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('hidden');
                    dropZone.classList.add('dragover');
                });
            });
            ['dragleave', 'drop'].forEach(event => {
                dropZone.addEventListener(event, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('hidden');
                    dropZone.classList.remove('dragover');
                });
            });
            mainContent.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.add('hidden');
                const files = e.dataTransfer.files;
                if (files.length) {
                    uploadQueue = Array.from(files);
                    uploadFilesDirectly();
                }
            });
            const uploadDropZone = document.getElementById('uploadDropZone');
            const fileInput = document.getElementById('fileInput');
            uploadDropZone.addEventListener('click', () => fileInput.click());
            uploadDropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadDropZone.classList.add('border-indigo-500');
            });
            uploadDropZone.addEventListener('dragleave', () => {
                uploadDropZone.classList.remove('border-indigo-500');
            });
            uploadDropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadDropZone.classList.remove('border-indigo-500');
                handleFileSelect(e.dataTransfer.files);
            });
            fileInput.addEventListener('change', (e) => handleFileSelect(e.target.files));
        }
        function handleFileSelect(files) {
            uploadQueue = Array.from(files);
            const list = document.getElementById('uploadList');
            list.innerHTML = uploadQueue.map(f => `
                <div class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-xl">
                    <span class="text-xl">${getFileIcon(f.name)}</span>
                    <span class="flex-1 truncate">${f.name}</span>
                    <span class="text-slate-400 text-sm">${formatBytes(f.size)}</span>
                </div>
            `).join('');
            document.getElementById('uploadBtn').disabled = uploadQueue.length === 0;
        }
        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const icons = {
                php: '🐘', html: '🌐', css: '🎨', js: '⚡', json: '📋',
                txt: '📄', md: '📝', pdf: '📕', zip: '📦', tar: '📦',
                jpg: '🖼️', jpeg: '🖼️', png: '🖼️', gif: '🖼️',
                mp4: '🎬', mp3: '🎵', sql: '🗃️',
            };
            return icons[ext] || '📄';
        }
        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        function showUploadTab(tab) {
            const filesTab = document.getElementById('uploadFilesTab');
            const urlTab = document.getElementById('uploadUrlTab');
            const tabFiles = document.getElementById('tabFiles');
            const tabUrl = document.getElementById('tabUrl');
            if (tab === 'files') {
                filesTab.classList.remove('hidden');
                urlTab.classList.add('hidden');
                tabFiles.classList.add('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabFiles.classList.remove('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
                tabUrl.classList.remove('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabUrl.classList.add('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
            } else {
                filesTab.classList.add('hidden');
                urlTab.classList.remove('hidden');
                tabUrl.classList.add('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabUrl.classList.remove('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
                tabFiles.classList.remove('bg-indigo-600/20', 'text-indigo-400', 'border-indigo-500/30');
                tabFiles.classList.add('bg-slate-700/30', 'text-slate-400', 'border-slate-600/30');
            }
        }
        async function loadFolderTree() {
            const formData = new FormData();
            formData.append('action', 'get_folder_tree');
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    const options = result.folders.map(f => 
                        `<option value="${f.path}">${'— '.repeat(f.depth)}📁 ${f.name}</option>`
                    ).join('');
                    document.getElementById('moveDestination').innerHTML = '<option value="">📁 Root</option>' + options;
                    document.getElementById('copyDestination').innerHTML = '<option value="">📁 Root</option>' + options;
                    document.getElementById('bulkMoveDestination').innerHTML = '<option value="">📁 Root</option>' + options;
                    document.getElementById('bulkCopyDestination').innerHTML = '<option value="">📁 Root</option>' + options;
                }
            } catch (e) {
                console.error('Failed to load folder tree:', e);
            }
        }
        let selectedItems = new Set();
        // Clipboard for copy/cut/paste operations - persisted in sessionStorage
        let clipboard = JSON.parse(sessionStorage.getItem('fm_clipboard')) || {
            items: [],
            operation: null, // 'copy' or 'cut'
            sourcePath: null
        };
        
        function saveClipboard() {
            sessionStorage.setItem('fm_clipboard', JSON.stringify(clipboard));
            updateClipboardIndicator();
        }
        
        function updateClipboardIndicator() {
            const indicator = document.getElementById('clipboardIndicator');
            const icon = document.getElementById('clipboardIcon');
            const text = document.getElementById('clipboardText');
            
            if (clipboard.items.length > 0) {
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
                icon.textContent = clipboard.operation === 'cut' ? '✂️' : '📋';
                const opText = clipboard.operation === 'cut' ? 'Cut' : 'Copied';
                text.textContent = `${opText}: ${clipboard.items.length} item${clipboard.items.length > 1 ? 's' : ''}`;
                
                // Add pulse animation for cut operation
                if (clipboard.operation === 'cut') {
                    indicator.classList.add('animate-pulse');
                } else {
                    indicator.classList.remove('animate-pulse');
                }
            } else {
                indicator.classList.add('hidden');
                indicator.classList.remove('flex', 'animate-pulse');
            }
        }
        
        function clearClipboard() {
            clipboard = { items: [], operation: null, sourcePath: null };
            saveClipboard();
            showToast('Clipboard cleared');
        }
        
        function showClipboardDetails() {
            if (clipboard.items.length === 0) return;
            const opText = clipboard.operation === 'cut' ? 'Cut' : 'Copied';
            const itemsList = clipboard.items.slice(0, 5).join(', ');
            const moreText = clipboard.items.length > 5 ? ` and ${clipboard.items.length - 5} more...` : '';
            showToast(`${opText} from "${clipboard.sourcePath || 'Root'}": ${itemsList}${moreText}`, 'info');
        }
        
        // Initialize clipboard indicator on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateClipboardIndicator();
            updateUndoIndicator();
        });
        
        // Undo stack for file operations - persisted in sessionStorage
        let undoStack = JSON.parse(sessionStorage.getItem('fm_undo_stack')) || [];
        const MAX_UNDO_STACK = 20;
        
        function saveUndoStack() {
            sessionStorage.setItem('fm_undo_stack', JSON.stringify(undoStack));
            updateUndoIndicator();
        }
        
        function pushUndo(operation) {
            undoStack.push({
                ...operation,
                timestamp: Date.now()
            });
            // Keep only last MAX_UNDO_STACK operations
            if (undoStack.length > MAX_UNDO_STACK) {
                undoStack = undoStack.slice(-MAX_UNDO_STACK);
            }
            saveUndoStack();
        }
        
        function updateUndoIndicator() {
            const indicator = document.getElementById('undoIndicator');
            const text = document.getElementById('undoText');
            
            if (undoStack.length > 0) {
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
                const lastOp = undoStack[undoStack.length - 1];
                const opLabels = {
                    'rename': 'Rename',
                    'move': 'Move', 
                    'delete': 'Delete',
                    'bulk_delete': 'Delete'
                };
                text.textContent = `Undo ${opLabels[lastOp.type] || lastOp.type}`;
            } else {
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
            }
        }
        
        async function performUndo() {
            if (undoStack.length === 0) {
                showToast('Nothing to undo', 'info');
                return;
            }
            
            const operation = undoStack.pop();
            saveUndoStack();
            
            const formData = new FormData();
            formData.append('action', 'undo_operation');
            formData.append('operation', JSON.stringify(operation));
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    showToast(`✅ Undo: ${result.message}`);
                    location.reload();
                } else {
                    // Put operation back if undo failed
                    undoStack.push(operation);
                    saveUndoStack();
                    showToast(result.error || 'Undo failed', 'error');
                }
            } catch (e) {
                undoStack.push(operation);
                saveUndoStack();
                showToast('Undo operation failed', 'error');
            }
        }
        
        function clearUndoStack() {
            undoStack = [];
            saveUndoStack();
            showToast('Undo history cleared');
        }
        function getSelectedItems() {
            return Array.from(selectedItems);
        }
        function toggleItemSelection(checkbox) {
            const name = checkbox.dataset.name;
            if (checkbox.checked) {
                selectedItems.add(name);
            } else {
                selectedItems.delete(name);
            }
            updateBulkToolbar();
        }
        function updateBulkToolbar() {
            const count = selectedItems.size;
            const toolbar = document.getElementById('bulkToolbar');
            const countSpan = document.getElementById('selectionCount');
            if (count > 0) {
                toolbar.classList.remove('hidden');
                countSpan.textContent = `${count} selected`;
            } else {
                toolbar.classList.add('hidden');
            }
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectAllGrid = document.getElementById('selectAllCheckbox');
            const selectAllList = document.getElementById('selectAllListCheckbox');
            const allChecked = allCheckboxes.length > 0 && count === allCheckboxes.length;
            if (selectAllGrid) selectAllGrid.checked = allChecked;
            if (selectAllList) selectAllList.checked = allChecked;
        }
        function toggleSelectAll(masterCheckbox) {
            const isChecked = masterCheckbox.checked;
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            allCheckboxes.forEach(cb => {
                cb.checked = isChecked;
                const name = cb.dataset.name;
                if (isChecked) {
                    selectedItems.add(name);
                } else {
                    selectedItems.delete(name);
                }
            });
            const selectAllGrid = document.getElementById('selectAllCheckbox');
            const selectAllList = document.getElementById('selectAllListCheckbox');
            if (selectAllGrid) selectAllGrid.checked = isChecked;
            if (selectAllList) selectAllList.checked = isChecked;
            updateBulkToolbar();
        }
        function clearSelection() {
            selectedItems.clear();
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
            updateBulkToolbar();
        }
        async function bulkDelete() {
            const items = getSelectedItems();
            if (items.length === 0) return;
            if (!confirm(`Delete ${items.length} item(s)? This cannot be undone.`)) return;
            const formData = new FormData();
            formData.append('action', 'bulk_delete');
            formData.append('items', JSON.stringify(items));
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(`Deleted ${result.deleted.length} item(s)`);
                    if (result.errors && result.errors.length > 0) {
                        showToast(result.errors.join(', '), 'error');
                    }
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Delete failed', 'error');
            }
        }
        function showBulkMoveModal() {
            const items = getSelectedItems();
            if (items.length === 0) return;
            document.getElementById('bulkMoveCount').textContent = `${items.length} item(s) selected`;
            loadFolderTree();
            showModal('bulkMoveModal');
        }
        async function bulkMove() {
            const items = getSelectedItems();
            if (items.length === 0) return;
            const destination = document.getElementById('bulkMoveDestination').value;
            const formData = new FormData();
            formData.append('action', 'bulk_move');
            formData.append('items', JSON.stringify(items));
            formData.append('destination', destination);
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(`Moved ${result.moved.length} item(s)`);
                    if (result.errors && result.errors.length > 0) {
                        showToast(result.errors.join(', '), 'error');
                    }
                    hideModal('bulkMoveModal');
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Move failed', 'error');
            }
        }
        function showBulkCopyModal() {
            const items = getSelectedItems();
            if (items.length === 0) return;
            document.getElementById('bulkCopyCount').textContent = `${items.length} item(s) selected`;
            loadFolderTree();
            showModal('bulkCopyModal');
        }
        async function bulkCopy() {
            const items = getSelectedItems();
            if (items.length === 0) return;
            const destination = document.getElementById('bulkCopyDestination').value;
            const formData = new FormData();
            formData.append('action', 'bulk_copy');
            formData.append('items', JSON.stringify(items));
            formData.append('destination', destination);
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(`Copied ${result.copied.length} item(s)`);
                    if (result.errors && result.errors.length > 0) {
                        showToast(result.errors.join(', '), 'error');
                    }
                    hideModal('bulkCopyModal');
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Copy failed', 'error');
            }
        }
        function showBulkCompressModal() {
            const items = getSelectedItems();
            if (items.length === 0) return;
            document.getElementById('bulkCompressCount').textContent = `${items.length} item(s) selected`;
            document.getElementById('bulkCompressName').value = 'archive';
            showModal('bulkCompressModal');
        }
        async function bulkCompress() {
            const items = getSelectedItems();
            if (items.length === 0) return;
            const name = document.getElementById('bulkCompressName').value.trim() || 'archive';
            const format = document.getElementById('bulkCompressFormat').value;
            const formData = new FormData();
            formData.append('action', 'bulk_compress');
            formData.append('items', JSON.stringify(items));
            formData.append('name', name);
            formData.append('format', format);
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(`Created: ${result.archive}`);
                    hideModal('bulkCompressModal');
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Compression failed', 'error');
            }
        }
        function showModal(id) {
            document.getElementById(id).classList.add('active');
        }
        function hideModal(id) {
            document.getElementById(id).classList.remove('active');
        }
        function hideContextMenu() {
            document.getElementById('contextMenu').classList.remove('active');
        }
        function setView(view) {
            currentView = view;
            localStorage.setItem('fm_view', view);
            const grid = document.getElementById('fileGrid');
            const list = document.getElementById('fileList');
            const viewGrid = document.getElementById('viewGrid');
            const viewList = document.getElementById('viewList');
            if (view === 'grid') {
                grid.classList.remove('hidden');
                list.classList.add('hidden');
                viewGrid.classList.add('bg-indigo-600/50');
                viewList.classList.remove('bg-indigo-600/50');
            } else {
                grid.classList.add('hidden');
                list.classList.remove('hidden');
                viewList.classList.add('bg-indigo-600/50');
                viewGrid.classList.remove('bg-indigo-600/50');
            }
        }
        function selectItem(event, element) {
            if (event.target.classList.contains('item-checkbox')) return;
            document.querySelectorAll('.file-item').forEach(item => item.classList.remove('selected'));
            element.classList.add('selected');
            selectedItem = {
                name: element.dataset.name,
                isDir: element.dataset.isDir === '1',
                size: parseInt(element.dataset.size || 0),
                modified: parseInt(element.dataset.modified || 0),
            };
        }
        function handleItemDblClick(element) {
            selectedItem = {
                name: element.dataset.name,
                isDir: element.dataset.isDir === '1',
            };
            openItem();
        }
        function showContextMenu(event, element) {
            event.preventDefault();
            selectItem(event, element);
            const menu = document.getElementById('contextMenu');
            const ctxEdit = document.getElementById('ctxEdit');
            const ctxPreview = document.getElementById('ctxPreview');
            const ctxExtract = document.getElementById('ctxExtract');
            const ctxShare = document.getElementById('ctxShare');
            const ctxScan = document.getElementById('ctxScan');
            if (selectedItem.isDir) {
                ctxEdit.classList.add('hidden');
                ctxPreview.classList.add('hidden');
                ctxExtract.classList.add('hidden');
                ctxShare.classList.add('hidden');
                ctxScan.classList.add('hidden');
            } else {
                ctxEdit.classList.remove('hidden');
                ctxPreview.classList.remove('hidden');
                ctxShare.classList.remove('hidden');
                ctxScan.classList.remove('hidden');
                const ext = selectedItem.name.split('.').pop().toLowerCase();
                if (['zip', 'tar', 'gz', 'bz2'].includes(ext)) {
                    ctxExtract.classList.remove('hidden');
                } else {
                    ctxExtract.classList.add('hidden');
                }
            }
            let x = event.clientX;
            let y = event.clientY;
            menu.style.left = x + 'px';
            menu.style.top = y + 'px';
            menu.classList.add('active');
            const rect = menu.getBoundingClientRect();
            if (rect.right > window.innerWidth) {
                menu.style.left = (window.innerWidth - rect.width - 10) + 'px';
            }
            if (rect.bottom > window.innerHeight) {
                menu.style.top = (window.innerHeight - rect.height - 10) + 'px';
            }
        }
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.context-menu')) {
                hideContextMenu();
            }
        });
        function openItem() {
            hideContextMenu();
            if (selectedItem.isDir) {
                const newPath = currentPath ? currentPath + '/' + selectedItem.name : selectedItem.name;
                window.location.href = '?path=' + encodeURIComponent(newPath);
            } else {
                editFile();
            }
        }
        function downloadFile() {
            hideContextMenu();
            if (selectedItem && !selectedItem.isDir) {
                window.location.href = '?path=' + encodeURIComponent(currentPath) + '&download=' + encodeURIComponent(selectedItem.name);
            }
        }
        function copyDirectUrl() {
            hideContextMenu();
            if (!selectedItem) return;
            const path = currentPath ? currentPath + '/' + selectedItem.name : selectedItem.name;
            const url = baseUrl + '?path=' + encodeURIComponent(currentPath) + '&preview=' + encodeURIComponent(selectedItem.name);
            navigator.clipboard.writeText(url).then(() => {
                showToast('URL copied to clipboard!');
            }).catch(() => {
                showToast('Failed to copy URL', 'error');
            });
        }
        function showRenameModal() {
            hideContextMenu();
            if (!selectedItem) return;
            document.getElementById('renameOldName').value = selectedItem.name;
            document.getElementById('renameNewName').value = selectedItem.name;
            showModal('renameModal');
        }
        async function renameItem() {
            const oldName = document.getElementById('renameOldName').value;
            const newName = document.getElementById('renameNewName').value.trim();
            if (!newName || newName === oldName) {
                hideModal('renameModal');
                return;
            }
            const formData = new FormData();
            formData.append('action', 'rename');
            formData.append('old_name', oldName);
            formData.append('new_name', newName);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                // Push to undo stack
                pushUndo({
                    type: 'rename',
                    path: currentPath,
                    oldName: oldName,
                    newName: newName
                });
                showToast('Renamed successfully');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        function showMoveModal() {
            if (!selectedItem) return;
            document.getElementById('moveFileName').value = selectedItem.name;
            loadFolderTree();
            showModal('moveModal');
        }
        async function moveItem() {
            const name = document.getElementById('moveFileName').value;
            const destination = document.getElementById('moveDestination').value;
            const formData = new FormData();
            formData.append('action', 'move');
            formData.append('name', name);
            formData.append('destination', destination);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                // Push to undo stack
                pushUndo({
                    type: 'move',
                    name: name,
                    source: currentPath,
                    destination: destination
                });
                showToast('Moved successfully');
                hideModal('moveModal');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        function showCopyModal() {
            if (!selectedItem) return;
            document.getElementById('copyFileName').value = selectedItem.name;
            loadFolderTree();
            showModal('copyModal');
        }
        async function copyItem() {
            const name = document.getElementById('copyFileName').value;
            const destination = document.getElementById('copyDestination').value;
            const formData = new FormData();
            formData.append('action', 'copy');
            formData.append('name', name);
            formData.append('destination', destination);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Copied successfully');
                hideModal('copyModal');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        async function deleteItem() {
            hideContextMenu();
            if (!selectedItem) return;
            if (!confirm(`Delete "${selectedItem.name}"? This cannot be undone.`)) return;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('name', selectedItem.name);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Deleted successfully');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        async function createFolder() {
            const name = document.getElementById('newFolderName').value.trim();
            if (!name) return;
            const formData = new FormData();
            formData.append('action', 'create_folder');
            formData.append('name', name);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Folder created');
                hideModal('createFolderModal');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        async function createFile() {
            const name = document.getElementById('newFileName').value.trim();
            if (!name) return;
            const formData = new FormData();
            formData.append('action', 'create_file');
            formData.append('name', name);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('File created');
                hideModal('createFileModal');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        async function uploadFiles() {
            const urlInput = document.getElementById('uploadUrlInput');
            if (!document.getElementById('uploadFilesTab').classList.contains('hidden')) {
                if (uploadQueue.length === 0) return;
                const formData = new FormData();
                formData.append('action', 'upload');
                formData.append('csrf_token', csrfToken);
                uploadQueue.forEach(file => formData.append('files[]', file));
                document.getElementById('uploadProgress').classList.remove('hidden');
                document.getElementById('uploadBtn').disabled = true;
                try {
                    const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
                    const result = await response.json();
                    if (result.success) {
                        showToast(`Uploaded ${result.files.length} file(s)`);
                        if (result.errors && result.errors.length > 0) {
                            showToast(result.errors.join(', '), 'error');
                        }
                        hideModal('uploadModal');
                        location.reload();
                    } else {
                        showToast(result.error, 'error');
                    }
                } catch (e) {
                    showToast('Upload failed', 'error');
                }
                document.getElementById('uploadProgress').classList.add('hidden');
                document.getElementById('uploadBtn').disabled = false;
            } else {
                const url = urlInput.value.trim();
                if (!url) return;
                const formData = new FormData();
                formData.append('action', 'upload_url');
                formData.append('url', url);
                formData.append('csrf_token', csrfToken);
                document.getElementById('uploadBtn').disabled = true;
                try {
                    const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
                    const result = await response.json();
                    if (result.success) {
                        showToast(`Downloaded: ${result.filename}`);
                        hideModal('uploadModal');
                        location.reload();
                    } else {
                        showToast(result.error, 'error');
                    }
                } catch (e) {
                    showToast('Download failed', 'error');
                }
                document.getElementById('uploadBtn').disabled = false;
            }
        }
        async function uploadFilesDirectly() {
            if (uploadQueue.length === 0) return;
            const formData = new FormData();
            formData.append('action', 'upload');
            formData.append('csrf_token', csrfToken);
            uploadQueue.forEach(file => formData.append('files[]', file));
            try {
                const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(`Uploaded ${result.files.length} file(s)`);
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Upload failed', 'error');
            }
        }
        function showCompressModal() {
            hideContextMenu();
            if (!selectedItem) return;
            showModal('compressModal');
        }
        async function compressItem() {
            if (!selectedItem) return;
            const format = document.getElementById('compressFormat').value;
            const formData = new FormData();
            formData.append('action', 'compress');
            formData.append('name', selectedItem.name);
            formData.append('format', format);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast(`Created: ${result.archive}`);
                hideModal('compressModal');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        async function extractArchive() {
            if (!selectedItem) return;
            const formData = new FormData();
            formData.append('action', 'extract');
            formData.append('name', selectedItem.name);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Extracted successfully');
                location.reload();
            } else {
                showToast(result.error, 'error');
            }
        }
        function showShareModal() {
            if (!selectedItem || selectedItem.isDir) return;
            document.getElementById('shareFileName').textContent = selectedItem.name;
            document.getElementById('shareExpiry').value = '24h';
            document.getElementById('shareOptions').classList.remove('hidden');
            document.getElementById('shareResult').classList.add('hidden');
            showModal('shareModal');
        }
        function resetShareModal() {
            document.getElementById('shareOptions').classList.remove('hidden');
            document.getElementById('shareResult').classList.add('hidden');
            document.getElementById('shareExpiry').value = '24h';
        }
        async function createShareLink() {
            if (!selectedItem) return;
            const expiry = document.getElementById('shareExpiry').value;
            const formData = new FormData();
            formData.append('action', 'create_share');
            formData.append('name', selectedItem.name);
            formData.append('expiry', expiry);
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    document.getElementById('shareUrlInput').value = result.share_url;
                    document.getElementById('shareExpiryInfo').textContent = result.expires_at 
                        ? `Expires: ${result.expires_at}` 
                        : 'This link never expires';
                    document.getElementById('shareOptions').classList.add('hidden');
                    document.getElementById('shareResult').classList.remove('hidden');
                    showToast('Share link created!');
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Failed to create share link', 'error');
            }
        }
        function copyShareUrl() {
            const input = document.getElementById('shareUrlInput');
            input.select();
            navigator.clipboard.writeText(input.value).then(() => {
                showToast('Share URL copied to clipboard!');
            }).catch(() => {
                document.execCommand('copy');
                showToast('Share URL copied to clipboard!');
            });
        }
        async function showManageSharesModal() {
            showModal('manageSharesModal');
            await loadShares();
        }
        async function loadShares() {
            const formData = new FormData();
            formData.append('action', 'get_shares');
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                const container = document.getElementById('sharesList');
                if (result.success && result.shares.length > 0) {
                    container.innerHTML = result.shares.map(share => `
                        <div class="glass rounded-xl p-4 border ${share.is_valid ? 'border-slate-700/50' : 'border-red-500/30 opacity-60'}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium truncate">${share.file_name}</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Created: ${share.created_at} • Downloads: ${share.downloads}
                                    </p>
                                    <p class="text-xs ${share.is_valid ? 'text-indigo-400' : 'text-red-400'} mt-1">
                                        ${share.expires_at ? (share.is_valid ? 'Expires: ' + share.expires_at : 'Expired') : 'Never expires'}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="copyToClipboard('${share.share_url}')" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors" title="Copy URL">
                                        📋
                                    </button>
                                    <button onclick="deleteShareLink('${share.token}')" class="p-2 hover:bg-red-600/20 text-red-400 rounded-lg transition-colors" title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-slate-500 text-center py-8">No share links found</p>';
                }
            } catch (e) {
                document.getElementById('sharesList').innerHTML = '<p class="text-red-400 text-center py-8">Failed to load shares</p>';
            }
        }
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('URL copied to clipboard!');
            }).catch(() => {
                const input = document.createElement('input');
                input.value = text;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('URL copied to clipboard!');
            });
        }
        async function deleteShareLink(token) {
            if (!confirm('Delete this share link?')) return;
            const formData = new FormData();
            formData.append('action', 'delete_share');
            formData.append('token', token);
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast('Share link deleted');
                    await loadShares();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Failed to delete share', 'error');
            }
        }
        async function cleanupExpiredShares() {
            const formData = new FormData();
            formData.append('action', 'cleanup_shares');
            formData.append('csrf_token', csrfToken);
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    showToast(`Cleaned up ${result.cleaned} expired share(s)`);
                    await loadShares();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('Cleanup failed', 'error');
            }
        }
        async function editFile() {
            if (!selectedItem || selectedItem.isDir) return;
            const formData = new FormData();
            formData.append('action', 'get_file_content');
            formData.append('name', selectedItem.name);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                currentEditingFile = selectedItem.name;
                document.getElementById('editorFileName').textContent = selectedItem.name;
                editor.setValue(result.content, -1);
                editor.session.setMode('ace/mode/' + result.mode);
                const savedTheme = localStorage.getItem('fm_editor_theme');
                if (savedTheme) {
                    editor.setTheme('ace/theme/' + savedTheme);
                    document.getElementById('editorTheme').value = savedTheme;
                }
                showModal('editorModal');
                // Force Ace Editor to recalculate dimensions after modal is visible
                setTimeout(() => {
                    editor.resize();
                    editor.renderer.updateFull();
                }, 50);
            } else {
                showToast(result.error, 'error');
            }
        }
        async function saveFile() {
            if (!currentEditingFile) return;
            const content = editor.getValue();

            const base64EncodeUtf8 = (value) => {
                try {
                    const bytes = new TextEncoder().encode(value);
                    let binary = '';
                    for (let i = 0; i < bytes.length; i++) binary += String.fromCharCode(bytes[i]);
                    return btoa(binary);
                } catch {
                    // Fallback (older browsers)
                    return btoa(unescape(encodeURIComponent(value)));
                }
            };

            const formData = new FormData();
            formData.append('action', 'save_file');
            formData.append('name', currentEditingFile);
            // Some hosts/WAF rules block POST bodies containing PHP code; base64 helps avoid false positives.
            const lowerName = String(currentEditingFile).toLowerCase();
            // Avoid literal "<" + "?" sequences in this PHP file (would be parsed by PHP).
            const phpOpenTag = '<' + '?php';
            const phpEchoTag = '<' + '?=';
            const looksLikePhp = lowerName.endsWith('.php') || content.includes(phpOpenTag) || content.includes(phpEchoTag);
            if (looksLikePhp) {
                formData.append('content_b64', base64EncodeUtf8(content));
            } else {
                formData.append('content', content);
            }
            formData.append('csrf_token', csrfToken);

            try {
                const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
                const raw = await response.text();

                let result;
                try {
                    result = raw ? JSON.parse(raw) : null;
                } catch (e) {
                    const statusInfo = `${response.status} ${response.statusText}`.trim();
                    const snippet = (raw || '').replace(/\s+/g, ' ').trim().slice(0, 220);
                    if (response.status === 403) {
                        showToast(`Save blocked (403). Hosting security/WAF may be blocking PHP content. ${snippet ? 'Response: ' + snippet : ''}`, 'error');
                    } else {
                        showToast(`Save failed (${statusInfo}). Server returned non-JSON. ${snippet ? 'Response: ' + snippet : ''}`, 'error');
                    }
                    return;
                }

                if (result && result.success) {
                    showToast('File saved');
                } else {
                    showToast((result && result.error) ? result.error : 'Save failed', 'error');
                }
            } catch (e) {
                showToast('Save failed: ' + (e && e.message ? e.message : 'Unknown error'), 'error');
            }
        }
        function previewFile() {
            if (!selectedItem || selectedItem.isDir) return;
            const ext = selectedItem.name.split('.').pop().toLowerCase();
            const url = `?path=${encodeURIComponent(currentPath)}&preview=${encodeURIComponent(selectedItem.name)}`;
            const previewContent = document.getElementById('previewContent');
            const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
            const videoExts = ['mp4', 'webm', 'ogg'];
            const audioExts = ['mp3', 'wav', 'ogg', 'flac', 'm4a'];
            const docExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
            if (imageExts.includes(ext)) {
                previewContent.innerHTML = `<img src="${url}" alt="${selectedItem.name}" class="max-w-full max-h-[80vh] rounded-xl shadow-2xl">`;
            } else if (videoExts.includes(ext)) {
                previewContent.innerHTML = `<video src="${url}" controls autoplay class="max-w-full max-h-[80vh] rounded-xl shadow-2xl"></video>`;
            } else if (audioExts.includes(ext)) {
                previewContent.innerHTML = `
                    <div class="glass p-8 rounded-2xl text-center">
                        <div class="text-6xl mb-4">🎵</div>
                        <p class="mb-4">${selectedItem.name}</p>
                        <audio src="${url}" controls autoplay class="w-full"></audio>
                    </div>`;
            } else if (docExts.includes(ext)) {
                const fullUrl = baseUrl + url;
                if (ext === 'pdf') {
                    previewContent.innerHTML = `<iframe src="${url}" class="w-full h-[80vh] rounded-xl bg-white"></iframe>`;
                } else {
                    previewContent.innerHTML = `<iframe src="https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + window.location.pathname + url)}&embedded=true" class="w-full h-[80vh] rounded-xl bg-white"></iframe>`;
                }
            }
            showModal('previewModal');
        }
        async function createBackup() {
            hideContextMenu();
            if (!selectedItem) return;
            const formData = new FormData();
            formData.append('action', 'backup');
            formData.append('name', selectedItem.name);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast(`Backup created: ${result.backup}`);
            } else {
                showToast(result.error, 'error');
            }
        }
        async function handleSearch(event) {
            if (event.key !== 'Enter') return;
            const query = document.getElementById('searchInput').value.trim();
            if (query.length < 2) {
                showToast('Enter at least 2 characters', 'error');
                return;
            }
            const formData = new FormData();
            formData.append('action', 'search');
            formData.append('query', query);
            formData.append('csrf_token', csrfToken);
            const response = await fetch('?path=' + encodeURIComponent(currentPath), { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                const container = document.getElementById('searchResults');
                if (result.results.length > 0) {
                    container.innerHTML = result.results.map(item => `
                        <a href="?path=${encodeURIComponent(item.is_dir ? item.path : item.path.split('/').slice(0, -1).join('/'))}" 
                           class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-700/50 transition-colors">
                            <span class="text-xl">${item.is_dir ? '📁' : getFileIcon(item.name)}</span>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">${item.name}</p>
                                <p class="text-xs text-slate-500 truncate">${item.path}</p>
                            </div>
                            ${!item.is_dir ? `<span class="text-xs text-slate-400">${formatBytes(item.size)}</span>` : ''}
                        </a>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-slate-500 text-center py-8">No results found</p>';
                }
                showModal('searchModal');
            } else {
                showToast(result.error, 'error');
            }
        }
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMessage');
            icon.textContent = type === 'success' ? '✅' : '❌';
            msg.textContent = message;
            toast.classList.add('active');
            setTimeout(() => toast.classList.remove('active'), 3000);
        }
        // Use CAPTURE PHASE (true) to intercept before browser handles shortcuts
        document.addEventListener('keydown', (e) => {
            const activeElement = document.activeElement;
            const isTyping = activeElement.tagName === 'INPUT' || 
                             activeElement.tagName === 'TEXTAREA' || 
                             activeElement.isContentEditable ||
                             (editor && editor.isFocused());
            const editorModalActive = document.getElementById('editorModal').classList.contains('active');
            const anyModalActive = document.querySelector('.modal.active');
            
            const key = e.key.toLowerCase();
            const ctrlOrCmd = e.ctrlKey || e.metaKey;
            
            // Escape - close modals/menus (always allow)
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(modal => {
                    if (modal.id !== 'editorModal' || !editor.isFocused()) {
                        modal.classList.remove('active');
                    }
                });
                document.getElementById('contextMenu').classList.remove('active');
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('overlay').classList.add('hidden');
                clearSelection();
                return;
            }
            
            // F11 - Toggle Editor Fullscreen (works even in editor)
            if (e.key === 'F11' && editorModalActive) {
                e.preventDefault();
                e.stopImmediatePropagation();
                toggleEditorFullscreen();
                return;
            }
            
            // Skip shortcuts if typing or modal active
            if (isTyping || anyModalActive) return;
            
            // ===== BROWSER SHORTCUT OVERRIDES =====
            // These MUST call preventDefault immediately to stop Chrome
            
            // Ctrl+N - New File (blocks Chrome new window)
            if (ctrlOrCmd && key === 'n' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showModal('createModal');
                return;
            }
            
            // Ctrl+Shift+N - New Folder (blocks Chrome incognito)
            if (ctrlOrCmd && e.shiftKey && key === 'n') {
                e.preventDefault();
                e.stopImmediatePropagation();
                document.getElementById('createType').value = 'folder';
                showModal('createModal');
                return;
            }
            
            // Ctrl+F - Search (blocks Chrome find)
            if (ctrlOrCmd && key === 'f' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showModal('searchModal');
                document.getElementById('searchQuery')?.focus();
                return;
            }
            
            // Ctrl+D - Download (blocks Chrome bookmark)
            if (ctrlOrCmd && key === 'd' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItem) downloadItem();
                return;
            }
            
            // Ctrl+U - Upload (blocks Chrome view source)
            if (ctrlOrCmd && key === 'u' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showModal('uploadModal');
                return;
            }
            
            // Ctrl+A - Select All
            if (ctrlOrCmd && key === 'a' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                selectAllItems();
                return;
            }
            
            // Ctrl+I - Check Permissions
            if (ctrlOrCmd && key === 'i' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItem) showPermissionsModal();
                return;
            }
            
            // Ctrl+Shift+M - Move
            if (ctrlOrCmd && e.shiftKey && key === 'm') {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItems.size > 0) showBulkMoveModal();
                return;
            }
            
            // Ctrl+Shift+C - Copy to destination
            if (ctrlOrCmd && e.shiftKey && key === 'c') {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItems.size > 0) showBulkCopyModal();
                return;
            }
            
            // Ctrl+Shift+Z - Compress
            if (ctrlOrCmd && e.shiftKey && key === 'z') {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItems.size > 0) showBulkCompressModal();
                return;
            }
            
            // Ctrl+C - Copy to clipboard
            if (ctrlOrCmd && key === 'c' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItems.size > 0 || selectedItem) copyToClipboardAction();
                return;
            }
            
            // Ctrl+X - Cut to clipboard
            if (ctrlOrCmd && key === 'x' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItems.size > 0 || selectedItem) cutToClipboardAction();
                return;
            }
            
            // Ctrl+V - Paste from clipboard
            if (ctrlOrCmd && key === 'v' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (clipboard.items.length > 0) pasteFromClipboardAction();
                return;
            }
            
            // Ctrl+Z - Undo
            if (ctrlOrCmd && key === 'z' && !e.shiftKey) {
                e.preventDefault();
                e.stopImmediatePropagation();
                performUndo();
                return;
            }
            
            // F5 - Refresh
            if (e.key === 'F5') {
                e.preventDefault();
                e.stopImmediatePropagation();
                loadFiles();
                return;
            }
            
            // F2 - Rename
            if (e.key === 'F2') {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItem) showRenameModal();
                return;
            }
            
            // Backspace - Go to parent folder
            if (e.key === 'Backspace') {
                e.preventDefault();
                e.stopImmediatePropagation();
                goUp();
                return;
            }
            
            // Delete - Delete selected
            if (e.key === 'Delete') {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (selectedItems.size > 0) {
                    bulkDelete();
                } else if (selectedItem) {
                    deleteItem();
                }
                return;
            }
            
            // Enter - Open item
            if (e.key === 'Enter' && selectedItem) {
                openItem();
                return;
            }
            
            // ? - Show shortcuts help
            if (e.key === '?') {
                e.preventDefault();
                showModal('shortcutsModal');
                return;
            }
        }, true); // CAPTURE PHASE - intercepts before browser
        function selectAllItems() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                const name = checkbox.dataset.name;
                const isDir = checkbox.dataset.isdir === 'true';
                selectedItems.add(JSON.stringify({ name, isDir }));
            });
            const masterCheckbox = document.getElementById('selectAllCheckbox');
            if (masterCheckbox) {
                masterCheckbox.checked = true;
                masterCheckbox.indeterminate = false;
            }
            updateBulkToolbar();
            showToast(`Selected ${selectedItems.size} item(s)`);
        }
        
        // Permissions Modal
        async function showPermissionsModal() {
            if (!selectedItem) {
                showToast('Please select a file or folder first', 'error');
                return;
            }
            hideContextMenu();
            showModal('permissionsModal');
            
            // Show loading, hide content
            document.getElementById('permissionsLoading').classList.remove('hidden');
            document.getElementById('permissionsContent').classList.add('hidden');
            
            const formData = new FormData();
            formData.append('action', 'get_permissions');
            formData.append('name', selectedItem.name);
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('?path=' + encodeURIComponent(currentPath), { 
                    method: 'POST', 
                    body: formData 
                });
                const result = await response.json();
                
                if (result.success) {
                    displayPermissions(result.permissions);
                } else {
                    showToast(result.error || 'Failed to get permissions', 'error');
                    hideModal('permissionsModal');
                }
            } catch (err) {
                showToast('Failed to get permissions: ' + err.message, 'error');
                hideModal('permissionsModal');
            }
        }
        
        function displayPermissions(perms) {
            document.getElementById('permissionsLoading').classList.add('hidden');
            document.getElementById('permissionsContent').classList.remove('hidden');
            
            // File info
            document.getElementById('permFileName').textContent = perms.name;
            document.getElementById('permFilePath').textContent = perms.path || '/';
            
            // Permission boxes
            const readBox = document.getElementById('permReadBox');
            const writeBox = document.getElementById('permWriteBox');
            const execBox = document.getElementById('permExecBox');
            
            // Read permission
            if (perms.readable) {
                readBox.className = 'text-center p-4 rounded-xl border bg-green-900/20 border-green-500/30';
                document.getElementById('permReadStatus').textContent = '✓ Allowed';
                document.getElementById('permReadStatus').className = 'text-xs text-green-400';
            } else {
                readBox.className = 'text-center p-4 rounded-xl border bg-red-900/20 border-red-500/30';
                document.getElementById('permReadStatus').textContent = '✗ Denied';
                document.getElementById('permReadStatus').className = 'text-xs text-red-400';
            }
            
            // Write permission
            if (perms.writable) {
                writeBox.className = 'text-center p-4 rounded-xl border bg-green-900/20 border-green-500/30';
                document.getElementById('permWriteStatus').textContent = '✓ Allowed';
                document.getElementById('permWriteStatus').className = 'text-xs text-green-400';
            } else {
                writeBox.className = 'text-center p-4 rounded-xl border bg-red-900/20 border-red-500/30';
                document.getElementById('permWriteStatus').textContent = '✗ Denied';
                document.getElementById('permWriteStatus').className = 'text-xs text-red-400';
            }
            
            // Execute permission
            if (perms.executable) {
                execBox.className = 'text-center p-4 rounded-xl border bg-green-900/20 border-green-500/30';
                document.getElementById('permExecStatus').textContent = '✓ Allowed';
                document.getElementById('permExecStatus').className = 'text-xs text-green-400';
            } else {
                execBox.className = 'text-center p-4 rounded-xl border bg-slate-800/50 border-slate-700/50';
                document.getElementById('permExecStatus').textContent = '— N/A';
                document.getElementById('permExecStatus').className = 'text-xs text-slate-400';
            }
            
            // Permission details
            document.getElementById('permOctal').textContent = perms.perms_octal;
            document.getElementById('permString').textContent = perms.perms_string;
            document.getElementById('permOwner').textContent = perms.owner || '—';
            document.getElementById('permGroup').textContent = perms.group || '—';
            
            // Ownership details
            const ownerIdStr = perms.owner_id !== null ? ` (UID: ${perms.owner_id})` : '';
            const groupIdStr = perms.group_id !== null ? ` (GID: ${perms.group_id})` : '';
            document.getElementById('ownerDetail').textContent = (perms.owner || '—') + ownerIdStr;
            document.getElementById('groupDetail').textContent = (perms.group || '—') + groupIdStr;
            document.getElementById('webUserDetail').textContent = perms.web_user || '—';
            
            // Ownership status indicator
            const ownershipStatus = document.getElementById('ownershipStatus');
            const sshSection = document.getElementById('sshCommandsSection');
            const chownRecursiveBox = document.getElementById('chownRecursiveBox');
            
            if (perms.is_owner) {
                ownershipStatus.innerHTML = `
                    <div class="flex items-center gap-2 text-green-400">
                        <span>✓</span>
                        <span class="text-sm">Web server owns this file</span>
                    </div>
                `;
                sshSection.classList.add('hidden');
            } else {
                ownershipStatus.innerHTML = `
                    <div class="flex items-center gap-2 text-amber-400">
                        <span>⚠️</span>
                        <span class="text-sm">Web server does not own this file</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Permission changes may fail. Consider changing ownership via SSH.</p>
                `;
                
                // Show SSH commands
                sshSection.classList.remove('hidden');
                if (perms.chown_commands) {
                    document.getElementById('chownSingle').textContent = perms.chown_commands.single;
                    document.getElementById('chownRecursive').textContent = perms.chown_commands.recursive;
                }
                
                // Show/hide recursive option based on item type
                if (perms.is_dir) {
                    chownRecursiveBox.classList.remove('hidden');
                } else {
                    chownRecursiveBox.classList.add('hidden');
                }
            }
            
            // Allowed operations
            const opsList = document.getElementById('permOpsList');
            const ops = [];
            if (perms.can_read) ops.push({ name: 'Read', icon: '📖', ok: true });
            if (perms.can_write) ops.push({ name: 'Write', icon: '✏️', ok: true });
            if (perms.can_delete) ops.push({ name: 'Delete', icon: '🗑️', ok: true });
            if (perms.can_rename) ops.push({ name: 'Rename', icon: '📝', ok: true });
            if (!perms.can_read) ops.push({ name: 'Read', icon: '📖', ok: false });
            if (!perms.can_write) ops.push({ name: 'Write', icon: '✏️', ok: false });
            if (!perms.can_delete) ops.push({ name: 'Delete', icon: '🗑️', ok: false });
            if (!perms.can_rename) ops.push({ name: 'Rename', icon: '📝', ok: false });
            
            opsList.innerHTML = ops.map(op => `
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs ${op.ok ? 'bg-green-600/20 text-green-400 border border-green-500/30' : 'bg-red-600/20 text-red-400 border border-red-500/30'}">
                    ${op.icon} ${op.name}
                </span>
            `).join('');
            
            // Warning message
            const warning = document.getElementById('permWarning');
            const warningText = document.getElementById('permWarningText');
            
            if (!perms.writable) {
                warning.classList.remove('hidden');
                if (perms.readable) {
                    warningText.textContent = 'This file is read-only. To modify it, change file permissions via SSH or cPanel File Manager (chmod 644 for files, 755 for folders).';
                } else {
                    warningText.textContent = 'You do not have access to this file. Contact your hosting provider to check file ownership and permissions.';
                }
            } else {
                warning.classList.add('hidden');
            }
            
            // Set chmod input with current permissions
            const chmodInput = document.getElementById('chmodInput');
            if (chmodInput) {
                chmodInput.value = perms.perms_octal.replace(/^0/, '');
            }
            
            // Reset chmod result
            const chmodResult = document.getElementById('chmodResult');
            if (chmodResult) {
                chmodResult.classList.add('hidden');
                chmodResult.innerHTML = '';
            }
            
            // Reset recursive checkbox and update visibility
            const recursiveCheckbox = document.getElementById('chmodRecursive');
            if (recursiveCheckbox) {
                recursiveCheckbox.checked = false;
            }
            const fileModeInput = document.getElementById('chmodFileMode');
            if (fileModeInput) {
                fileModeInput.value = '';
            }
            
            // Update recursive chmod UI visibility based on item type
            updateRecursiveChmodUI();
        }
        
        function setChmodPreset(mode) {
            const chmodInput = document.getElementById('chmodInput');
            if (chmodInput) {
                chmodInput.value = mode;
            }
        }
        
        function copyCommand(elementId) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const command = element.textContent;
            navigator.clipboard.writeText(command).then(() => {
                showToast('Command copied to clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = command;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showToast('Command copied to clipboard!');
            });
        }
        
        function updateRecursiveChmodUI() {
            const recursiveSection = document.getElementById('chmodRecursiveSection');
            const fileModeSection = document.getElementById('chmodFileModeSection');
            const recursiveCheckbox = document.getElementById('chmodRecursive');
            
            if (!selectedItem || !recursiveSection) return;
            
            // Show recursive option only for directories
            if (selectedItem.is_dir) {
                recursiveSection.classList.remove('hidden');
            } else {
                recursiveSection.classList.add('hidden');
                if (recursiveCheckbox) recursiveCheckbox.checked = false;
            }
            
            // Show file mode input when recursive is checked
            if (fileModeSection && recursiveCheckbox) {
                if (recursiveCheckbox.checked) {
                    fileModeSection.classList.remove('hidden');
                } else {
                    fileModeSection.classList.add('hidden');
                }
            }
        }
        
        // Add event listener for recursive checkbox
        document.addEventListener('DOMContentLoaded', () => {
            const recursiveCheckbox = document.getElementById('chmodRecursive');
            if (recursiveCheckbox) {
                recursiveCheckbox.addEventListener('change', updateRecursiveChmodUI);
            }
        });
        
        async function attemptChmod() {
            if (!selectedItem) {
                showToast('No file selected', 'error');
                return;
            }
            
            const chmodInput = document.getElementById('chmodInput');
            const chmodBtn = document.getElementById('chmodBtn');
            const chmodResult = document.getElementById('chmodResult');
            const chmodProgress = document.getElementById('chmodProgress');
            const recursiveCheckbox = document.getElementById('chmodRecursive');
            const fileModeInput = document.getElementById('chmodFileMode');
            const mode = chmodInput.value.trim();
            
            if (!mode || !/^[0-7]{3,4}$/.test(mode)) {
                showToast('Invalid permission mode. Use octal format like 755 or 644', 'error');
                return;
            }
            
            const isRecursive = recursiveCheckbox && recursiveCheckbox.checked && selectedItem.is_dir;
            const fileMode = fileModeInput ? fileModeInput.value.trim() : '';
            
            // Validate file mode if provided
            if (isRecursive && fileMode && !/^[0-7]{3,4}$/.test(fileMode)) {
                showToast('Invalid file mode. Use octal format like 644', 'error');
                return;
            }
            
            // Confirm recursive operation
            if (isRecursive) {
                const confirmed = confirm(`Apply chmod ${mode} recursively to all files and subfolders in "${selectedItem.name}"?\n\nThis operation cannot be undone.`);
                if (!confirmed) return;
            }
            
            // Disable button during operation
            chmodBtn.disabled = true;
            chmodBtn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> ' + (isRecursive ? 'Scanning...' : 'Applying...');
            
            // Hide previous results
            chmodResult.classList.add('hidden');
            
            if (isRecursive) {
                // Use SSE for recursive operations with progress
                await attemptChmodRecursiveWithProgress(mode, fileMode, chmodBtn, chmodResult, chmodProgress);
            } else {
                // Use regular fetch for single file/folder
                await attemptChmodSingle(mode, chmodBtn, chmodResult);
            }
        }
        
        async function attemptChmodSingle(mode, chmodBtn, chmodResult) {
            const formData = new FormData();
            formData.append('action', 'attempt_chmod');
            formData.append('name', selectedItem.name);
            formData.append('mode', mode);
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('?path=' + encodeURIComponent(currentPath), { 
                    method: 'POST', 
                    body: formData 
                });
                const result = await response.json();
                
                chmodResult.classList.remove('hidden');
                
                if (result.success) {
                    chmodResult.className = 'mt-3 p-3 rounded-lg text-sm bg-green-900/30 border border-green-500/30';
                    chmodResult.innerHTML = `
                        <div class="flex items-start gap-2">
                            <span class="text-green-400">✓</span>
                            <div>
                                <p class="text-green-400 font-medium">${result.message}</p>
                                <p class="text-xs text-slate-400 mt-1">
                                    Changed from <code class="bg-slate-700/50 px-1 rounded">${result.old_perms}</code> 
                                    to <code class="bg-slate-700/50 px-1 rounded">${result.new_perms}</code>
                                </p>
                            </div>
                        </div>
                    `;
                    showToast('Permissions changed successfully');
                    setTimeout(() => showPermissionsModal(), 1500);
                } else {
                    chmodResult.className = 'mt-3 p-3 rounded-lg text-sm bg-red-900/30 border border-red-500/30';
                    let errorHtml = `
                        <div class="flex items-start gap-2">
                            <span class="text-red-400">✗</span>
                            <div>
                                <p class="text-red-400 font-medium">chmod Failed</p>
                                <p class="text-xs text-slate-300 mt-1 whitespace-pre-line">${result.error}</p>
                    `;
                    if (result.technical_error) {
                        errorHtml += `<p class="text-xs text-slate-500 mt-2 font-mono">${result.technical_error}</p>`;
                    }
                    errorHtml += '</div></div>';
                    chmodResult.innerHTML = errorHtml;
                }
            } catch (err) {
                chmodResult.classList.remove('hidden');
                chmodResult.className = 'mt-3 p-3 rounded-lg text-sm bg-red-900/30 border border-red-500/30';
                chmodResult.innerHTML = `
                    <div class="flex items-start gap-2">
                        <span class="text-red-400">✗</span>
                        <div>
                            <p class="text-red-400 font-medium">Request Failed</p>
                            <p class="text-xs text-slate-400 mt-1">${err.message}</p>
                        </div>
                    </div>
                `;
            } finally {
                chmodBtn.disabled = false;
                chmodBtn.innerHTML = '<span>⚡</span> Apply';
            }
        }
        
        async function attemptChmodRecursiveWithProgress(mode, fileMode, chmodBtn, chmodResult, chmodProgress) {
            return new Promise((resolve) => {
                // Show progress UI
                chmodProgress.classList.remove('hidden');
                const progressBar = document.getElementById('chmodProgressBar');
                const progressPercent = document.getElementById('chmodProgressPercent');
                const progressCurrent = document.getElementById('chmodProgressCurrent');
                const progressStats = document.getElementById('chmodProgressStats');
                const successCount = document.getElementById('chmodSuccessCount');
                const failedCount = document.getElementById('chmodFailedCount');
                
                // Reset progress UI
                progressBar.style.width = '0%';
                progressPercent.textContent = '0%';
                progressCurrent.textContent = 'Counting files...';
                progressStats.textContent = '0 / 0';
                successCount.textContent = '0';
                failedCount.textContent = '0';
                
                // Build URL with params for SSE (can't send POST body with EventSource)
                // We'll use a workaround by making a POST request that returns SSE
                const formData = new FormData();
                formData.append('action', 'chmod_recursive_stream');
                formData.append('name', selectedItem.name);
                formData.append('mode', mode);
                formData.append('file_mode', fileMode);
                formData.append('csrf_token', csrfToken);
                
                // Use fetch with streaming response
                fetch('?path=' + encodeURIComponent(currentPath), {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let buffer = '';
                    
                    function processStream() {
                        return reader.read().then(({ done, value }) => {
                            if (done) {
                                chmodProgress.classList.add('hidden');
                                chmodBtn.disabled = false;
                                chmodBtn.innerHTML = '<span>⚡</span> Apply';
                                resolve();
                                return;
                            }
                            
                            buffer += decoder.decode(value, { stream: true });
                            const lines = buffer.split('\n');
                            buffer = lines.pop() || '';
                            
                            for (const line of lines) {
                                if (line.startsWith('data: ')) {
                                    try {
                                        const data = JSON.parse(line.substring(6));
                                        handleChmodProgressEvent(data, progressBar, progressPercent, progressCurrent, progressStats, successCount, failedCount, chmodResult, chmodBtn, chmodProgress);
                                    } catch (e) {
                                        console.warn('Failed to parse SSE data:', line);
                                    }
                                }
                            }
                            
                            return processStream();
                        });
                    }
                    
                    return processStream();
                }).catch(err => {
                    chmodProgress.classList.add('hidden');
                    chmodResult.classList.remove('hidden');
                    chmodResult.className = 'mt-3 p-3 rounded-lg text-sm bg-red-900/30 border border-red-500/30';
                    chmodResult.innerHTML = `
                        <div class="flex items-start gap-2">
                            <span class="text-red-400">✗</span>
                            <div>
                                <p class="text-red-400 font-medium">Request Failed</p>
                                <p class="text-xs text-slate-400 mt-1">${err.message}</p>
                            </div>
                        </div>
                    `;
                    chmodBtn.disabled = false;
                    chmodBtn.innerHTML = '<span>⚡</span> Apply';
                    resolve();
                });
            });
        }
        
        function handleChmodProgressEvent(data, progressBar, progressPercent, progressCurrent, progressStats, successCountEl, failedCountEl, chmodResult, chmodBtn, chmodProgress) {
            switch (data.type) {
                case 'start':
                    chmodBtn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> Processing...';
                    progressCurrent.textContent = `Processing ${data.name}...`;
                    progressStats.textContent = `0 / ${data.total}`;
                    break;
                    
                case 'progress':
                    progressBar.style.width = data.percent + '%';
                    progressPercent.textContent = data.percent + '%';
                    progressCurrent.textContent = data.current;
                    progressStats.textContent = `${data.processed} / ${data.total}`;
                    successCountEl.textContent = data.success;
                    failedCountEl.textContent = data.failed;
                    break;
                    
                case 'complete':
                    chmodProgress.classList.add('hidden');
                    chmodResult.classList.remove('hidden');
                    
                    if (data.success > 0) {
                        chmodResult.className = 'mt-3 p-3 rounded-lg text-sm bg-green-900/30 border border-green-500/30';
                        let successHtml = `
                            <div class="flex items-start gap-2">
                                <span class="text-green-400">✓</span>
                                <div>
                                    <p class="text-green-400 font-medium">Recursive chmod completed</p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Changed from <code class="bg-slate-700/50 px-1 rounded">${data.old_perms}</code> 
                                        to <code class="bg-slate-700/50 px-1 rounded">${data.new_perms}</code>
                                    </p>
                                    <p class="text-xs text-slate-400 mt-2">
                                        <span class="text-green-400">${data.success}</span> items changed
                                        ${data.failed > 0 ? `, <span class="text-yellow-400">${data.failed}</span> failed` : ''}
                                    </p>
                        `;
                        if (data.errors && data.errors.length > 0) {
                            successHtml += `<p class="text-xs text-yellow-400/80 mt-1">Errors: ${data.errors.slice(0, 3).join(', ')}${data.errors.length > 3 ? '...' : ''}</p>`;
                        }
                        successHtml += '</div></div>';
                        chmodResult.innerHTML = successHtml;
                        showToast('Recursive chmod completed');
                        setTimeout(() => showPermissionsModal(), 1500);
                    } else {
                        chmodResult.className = 'mt-3 p-3 rounded-lg text-sm bg-red-900/30 border border-red-500/30';
                        let errorHtml = `
                            <div class="flex items-start gap-2">
                                <span class="text-red-400">✗</span>
                                <div>
                                    <p class="text-red-400 font-medium">Recursive chmod failed</p>
                                    <p class="text-xs text-slate-300 mt-1">No items were changed.</p>
                        `;
                        if (data.errors && data.errors.length > 0) {
                            errorHtml += `<p class="text-xs text-slate-500 mt-2">Errors: ${data.errors.slice(0, 3).join(', ')}${data.errors.length > 3 ? '...' : ''}</p>`;
                        }
                        errorHtml += '</div></div>';
                        chmodResult.innerHTML = errorHtml;
                    }
                    break;
                    
                case 'error':
                    chmodProgress.classList.add('hidden');
                    chmodResult.classList.remove('hidden');
                    chmodResult.className = 'mt-3 p-3 rounded-lg text-sm bg-red-900/30 border border-red-500/30';
                    chmodResult.innerHTML = `
                        <div class="flex items-start gap-2">
                            <span class="text-red-400">✗</span>
                            <div>
                                <p class="text-red-400 font-medium">Error</p>
                                <p class="text-xs text-slate-400 mt-1">${data.error}</p>
                            </div>
                        </div>
                    `;
                    break;
            }
        }
        
        // Clipboard operations
        function copyToClipboardAction() {
            const items = selectedItems.size > 0 ? getSelectedItems() : (selectedItem ? [selectedItem.name] : []);
            if (items.length === 0) {
                showToast('No items selected', 'error');
                return;
            }
            clipboard.items = items;
            clipboard.operation = 'copy';
            clipboard.sourcePath = currentPath;
            saveClipboard();
            showToast(`📋 Copied ${items.length} item(s) to clipboard`);
        }
        
        function cutToClipboardAction() {
            const items = selectedItems.size > 0 ? getSelectedItems() : (selectedItem ? [selectedItem.name] : []);
            if (items.length === 0) {
                showToast('No items selected', 'error');
                return;
            }
            clipboard.items = items;
            clipboard.operation = 'cut';
            clipboard.sourcePath = currentPath;
            saveClipboard();
            showToast(`✂️ Cut ${items.length} item(s) to clipboard`);
        }
        
        async function pasteFromClipboardAction() {
            if (clipboard.items.length === 0) {
                showToast('Clipboard is empty', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', clipboard.operation === 'cut' ? 'clipboard_move' : 'clipboard_copy');
            formData.append('items', JSON.stringify(clipboard.items));
            formData.append('source_path', clipboard.sourcePath);
            formData.append('destination', currentPath);
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    const actionText = clipboard.operation === 'cut' ? 'Moved' : 'Copied';
                    showToast(`${actionText} ${result.processed?.length || clipboard.items.length} item(s)`);
                    
                    // Clear clipboard after cut operation
                    if (clipboard.operation === 'cut') {
                        clipboard = { items: [], operation: null, sourcePath: null };
                        saveClipboard();
                    }
                    
                    if (result.errors && result.errors.length > 0) {
                        showToast(result.errors.join(', '), 'error');
                    }
                    location.reload();
                } else {
                    showToast(result.error || 'Paste failed', 'error');
                }
            } catch (e) {
                showToast('Paste operation failed', 'error');
            }
        }
        
        function scanSingleFile() {
            if (!selectedItem || selectedItem.isDir) {
                showToast('Please select a file to scan', 'error');
                return;
            }
            hideContextMenu();
            showModal('scanModal');
            document.getElementById('scanLoading').classList.remove('hidden');
            document.getElementById('scanResults').classList.add('hidden');
            document.getElementById('scanClean').classList.add('hidden');
            document.getElementById('scanSummary').classList.add('hidden');
            const formData = new FormData();
            formData.append('action', 'scan_file');
            formData.append('name', selectedItem.name);
            formData.append('csrf_token', csrfToken);
            fetch('?path=' + encodeURIComponent(currentPath), {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('scanLoading').classList.add('hidden');
                if (data.success && data.result) {
                    displayScanResults({
                        scanned: 1,
                        infected: data.result.is_clean ? 0 : 1,
                        critical: 0,
                        high: 0,
                        medium: 0,
                        results: data.result.is_clean ? [] : [{
                            file: data.result.file,
                            findings: data.result.findings,
                            total_issues: data.result.total_issues
                        }]
                    });
                    if (data.result.findings) {
                        data.result.findings.forEach(f => {
                            if (f.severity === 'critical') document.getElementById('scanCritical').textContent = 
                                parseInt(document.getElementById('scanCritical').textContent) + 1;
                            if (f.severity === 'high') document.getElementById('scanHigh').textContent = 
                                parseInt(document.getElementById('scanHigh').textContent) + 1;
                            if (f.severity === 'medium') document.getElementById('scanMedium').textContent = 
                                parseInt(document.getElementById('scanMedium').textContent) + 1;
                        });
                    }
                } else {
                    showToast(data.result?.error || 'Scan failed', 'error');
                    hideModal('scanModal');
                }
            })
            .catch(err => {
                document.getElementById('scanLoading').classList.add('hidden');
                showToast('Scan failed: ' + err.message, 'error');
                hideModal('scanModal');
            });
        }
        function scanAllFiles() {
            showModal('scanModal');
            document.getElementById('scanLoading').classList.remove('hidden');
            document.getElementById('scanResults').innerHTML = '';
            document.getElementById('scanResults').classList.add('hidden');
            document.getElementById('scanClean').classList.add('hidden');
            document.getElementById('scanSummary').classList.add('hidden');
            document.getElementById('scanScanned').textContent = '0';
            document.getElementById('scanCritical').textContent = '0';
            document.getElementById('scanHigh').textContent = '0';
            document.getElementById('scanMedium').textContent = '0';
            const formData = new FormData();
            formData.append('action', 'scan_all');
            formData.append('csrf_token', csrfToken);
            fetch('?path=' + encodeURIComponent(currentPath), {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('scanLoading').classList.add('hidden');
                if (data.success && data.result) {
                    displayScanResults(data.result);
                } else {
                    showToast(data.error || 'Scan failed', 'error');
                    hideModal('scanModal');
                }
            })
            .catch(err => {
                document.getElementById('scanLoading').classList.add('hidden');
                showToast('Scan failed: ' + err.message, 'error');
                hideModal('scanModal');
            });
        }
        function displayScanResults(result) {
            document.getElementById('scanSummary').classList.remove('hidden');
            document.getElementById('scanScanned').textContent = result.scanned;
            document.getElementById('scanCritical').textContent = result.critical;
            document.getElementById('scanHigh').textContent = result.high;
            document.getElementById('scanMedium').textContent = result.medium;
            const resultsContainer = document.getElementById('scanResults');
            const cleanMessage = document.getElementById('scanClean');
            if (result.results.length === 0) {
                cleanMessage.classList.remove('hidden');
                cleanMessage.classList.add('flex');
                resultsContainer.classList.add('hidden');
            } else {
                cleanMessage.classList.add('hidden');
                resultsContainer.classList.remove('hidden');
                resultsContainer.innerHTML = result.results.map(file => `
                    <div class="bg-slate-800/50 rounded-xl border border-red-500/30 overflow-hidden">
                        <div class="px-4 py-3 bg-red-900/20 border-b border-red-500/30 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">⚠️</span>
                                <span class="font-medium text-red-300">${escapeHtml(file.file)}</span>
                            </div>
                            <span class="text-xs bg-red-600/30 text-red-300 px-2 py-1 rounded-full">${file.total_issues} issue(s)</span>
                        </div>
                        <div class="p-4 space-y-3">
                            ${file.findings.map(finding => `
                                <div class="flex items-start gap-3 p-3 rounded-lg ${getSeverityBg(finding.severity)}">
                                    <span class="text-lg">${getSeverityIcon(finding.severity)}</span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-medium ${getSeverityColor(finding.severity)}">${escapeHtml(finding.description)}</span>
                                            <span class="text-xs px-2 py-0.5 rounded-full ${getSeverityBadge(finding.severity)}">${finding.severity.toUpperCase()}</span>
                                            <span class="text-xs text-slate-500">${escapeHtml(finding.category)}</span>
                                        </div>
                                        <div class="text-xs text-slate-400 mt-1">Line ${finding.line}</div>
                                        <code class="text-xs bg-slate-900/50 text-slate-300 px-2 py-1 rounded mt-2 block overflow-x-auto whitespace-nowrap">${escapeHtml(finding.line_content)}</code>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `).join('');
            }
        }
        function getSeverityBg(severity) {
            switch(severity) {
                case 'critical': return 'bg-red-900/20';
                case 'high': return 'bg-orange-900/20';
                case 'medium': return 'bg-yellow-900/20';
                default: return 'bg-slate-800/50';
            }
        }
        function getSeverityColor(severity) {
            switch(severity) {
                case 'critical': return 'text-red-400';
                case 'high': return 'text-orange-400';
                case 'medium': return 'text-yellow-400';
                default: return 'text-slate-300';
            }
        }
        function getSeverityIcon(severity) {
            switch(severity) {
                case 'critical': return '🔴';
                case 'high': return '🟠';
                case 'medium': return '🟡';
                default: return '🔵';
            }
        }
        function getSeverityBadge(severity) {
            switch(severity) {
                case 'critical': return 'bg-red-600/30 text-red-300';
                case 'high': return 'bg-orange-600/30 text-orange-300';
                case 'medium': return 'bg-yellow-600/30 text-yellow-300';
                default: return 'bg-slate-600/30 text-slate-300';
            }
        }
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }
        let pmaInstalled = false;
        let pmaUrl = '';
        function showPhpMyAdminModal() {
            showModal('phpmyadminModal');
            checkPhpMyAdminStatus();
        }
        function checkPhpMyAdminStatus() {
            const statusIcon = document.getElementById('pmaStatusIcon');
            const statusTitle = document.getElementById('pmaStatusTitle');
            const statusDesc = document.getElementById('pmaStatusDesc');
            const installBtn = document.getElementById('pmaInstallBtn');
            const openBtn = document.getElementById('pmaOpenBtn');
            const uninstallBtn = document.getElementById('pmaUninstallBtn');
            statusIcon.textContent = '🔍';
            statusTitle.textContent = 'Checking status...';
            statusDesc.textContent = 'Please wait';
            installBtn.classList.add('hidden');
            openBtn.classList.add('hidden');
            uninstallBtn.classList.add('hidden');
            const formData = new FormData();
            formData.append('action', 'check_phpmyadmin');
            formData.append('csrf_token', csrfToken);
            formData.append('path', currentPath);
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.installed) {
                        pmaInstalled = true;
                        pmaUrl = data.url;
                        statusIcon.textContent = '✅';
                        statusTitle.textContent = 'phpMyAdmin Installed';
                        statusDesc.textContent = 'Ready to use';
                        openBtn.classList.remove('hidden');
                        uninstallBtn.classList.remove('hidden');
                    } else {
                        pmaInstalled = false;
                        statusIcon.textContent = '📦';
                        statusTitle.textContent = 'Not Installed';
                        statusDesc.textContent = 'Click below to install phpMyAdmin';
                        installBtn.classList.remove('hidden');
                    }
                } else {
                    statusIcon.textContent = '❌';
                    statusTitle.textContent = 'Error';
                    statusDesc.textContent = data.error || 'Failed to check status';
                    installBtn.classList.remove('hidden');
                }
            })
            .catch(err => {
                statusIcon.textContent = '❌';
                statusTitle.textContent = 'Error';
                statusDesc.textContent = 'Failed to check status';
                installBtn.classList.remove('hidden');
            });
        }
        function installPhpMyAdmin() {
            const statusSection = document.getElementById('pmaStatus');
            const progressSection = document.getElementById('pmaProgress');
            const actionsSection = document.getElementById('pmaActions');
            const progressBar = document.getElementById('pmaProgressBar');
            const progressTitle = document.getElementById('pmaProgressTitle');
            const progressDesc = document.getElementById('pmaProgressDesc');
            statusSection.classList.add('hidden');
            progressSection.classList.remove('hidden');
            actionsSection.classList.add('hidden');
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                progressBar.style.width = progress + '%';
            }, 500);
            const formData = new FormData();
            formData.append('action', 'install_phpmyadmin');
            formData.append('csrf_token', csrfToken);
            formData.append('path', currentPath);
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                setTimeout(() => {
                    statusSection.classList.remove('hidden');
                    progressSection.classList.add('hidden');
                    actionsSection.classList.remove('hidden');
                    if (data.success) {
                        pmaUrl = data.url;
                        showToast(data.message || 'phpMyAdmin installed successfully!', 'success');
                        checkPhpMyAdminStatus();
                        location.reload();
                    } else {
                        showToast(data.error || 'Installation failed', 'error');
                        checkPhpMyAdminStatus();
                    }
                }, 500);
            })
            .catch(err => {
                clearInterval(progressInterval);
                statusSection.classList.remove('hidden');
                progressSection.classList.add('hidden');
                actionsSection.classList.remove('hidden');
                showToast('Installation failed: ' + err.message, 'error');
                checkPhpMyAdminStatus();
            });
        }
        function openPhpMyAdmin() {
            if (pmaUrl) {
                window.open(pmaUrl, '_blank');
            } else {
                showToast('phpMyAdmin URL not found', 'error');
            }
        }
        function uninstallPhpMyAdmin() {
            if (!confirm('Are you sure you want to uninstall phpMyAdmin? This will delete all phpMyAdmin files.')) {
                return;
            }
            const formData = new FormData();
            formData.append('action', 'uninstall_phpmyadmin');
            formData.append('csrf_token', csrfToken);
            formData.append('path', currentPath);
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'phpMyAdmin uninstalled successfully!');
                    checkPhpMyAdminStatus();
                    location.reload();
                } else {
                    showToast(data.error || 'Uninstallation failed', 'error');
                }
            })
            .catch(err => {
                showToast('Uninstallation failed: ' + err.message, 'error');
            });
        }
        
        // Security Dashboard Functions
        function showSecurityDashboard() {
            showModal('securityDashboardModal');
            loadSecurityStatus();
            loadBlockedIPs();
        }
        
        async function loadSecurityStatus() {
            const formData = new FormData();
            formData.append('action', 'get_security_status');
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    const status = result.status;
                    
                    // HTTPS Status
                    const httpsEl = document.getElementById('statusHttps');
                    const httpsStatusEl = document.getElementById('httpsStatus');
                    if (status.https_enabled) {
                        httpsEl.textContent = '✅';
                        httpsStatusEl.textContent = 'Enabled';
                        httpsStatusEl.className = 'text-lg font-semibold text-green-400';
                    } else {
                        httpsEl.textContent = '⚠️';
                        httpsStatusEl.textContent = 'Not Enabled';
                        httpsStatusEl.className = 'text-lg font-semibold text-yellow-400';
                    }
                    
                    // Audit Status
                    const auditEl = document.getElementById('auditStatus');
                    auditEl.textContent = status.audit_logging ? 'Active' : 'Disabled';
                    auditEl.className = status.audit_logging ? 'text-lg font-semibold text-green-400' : 'text-lg font-semibold text-red-400';
                    
                    // Blocked IPs Count
                    document.getElementById('blockedIpsCount').textContent = status.blocked_ips_count;
                    
                    // Session Timeout
                    const minutes = Math.floor(status.session_timeout / 60);
                    document.getElementById('sessionTimeout').textContent = minutes + ' min';
                }
            } catch (err) {
                console.error('Failed to load security status:', err);
            }
        }
        
        async function loadBlockedIPs() {
            const formData = new FormData();
            formData.append('action', 'get_blocked_ips');
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                const container = document.getElementById('blockedIpsList');
                
                if (result.success) {
                    if (result.blocked.length === 0) {
                        container.innerHTML = '<p class="text-slate-500 text-sm">No blocked IPs</p>';
                    } else {
                        container.innerHTML = result.blocked.map(item => `
                            <div class="flex items-center justify-between bg-red-900/20 border border-red-500/30 rounded-lg p-3">
                                <div>
                                    <span class="font-mono text-red-300">${escapeHtml(item.ip)}</span>
                                    <span class="text-xs text-slate-500 ml-2">${item.attempts} failed attempts</span>
                                    <div class="text-xs text-slate-400 mt-1">Unlocks: ${item.locked_until}</div>
                                </div>
                                <button onclick="unblockIP('${item.ip}')" class="btn-action btn-secondary text-sm py-1.5">
                                    Unblock
                                </button>
                            </div>
                        `).join('');
                    }
                }
            } catch (err) {
                console.error('Failed to load blocked IPs:', err);
            }
        }
        
        async function unblockIP(ip) {
            const formData = new FormData();
            formData.append('action', 'unblock_ip');
            formData.append('ip', ip);
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    showToast('IP unblocked successfully');
                    loadBlockedIPs();
                    loadSecurityStatus();
                } else {
                    showToast(result.error || 'Failed to unblock IP', 'error');
                }
            } catch (err) {
                showToast('Failed to unblock IP', 'error');
            }
        }
        
        // Audit Log Functions
        function showAuditLogModal() {
            showModal('auditLogModal');
            loadAuditLog();
        }
        
        async function loadAuditLog() {
            const lines = document.getElementById('auditLogLines').value;
            const formData = new FormData();
            formData.append('action', 'get_audit_log');
            formData.append('lines', lines);
            formData.append('csrf_token', csrfToken);
            
            document.getElementById('auditLogContent').textContent = 'Loading...';
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    const content = result.log || 'No log entries yet.';
                    document.getElementById('auditLogContent').innerHTML = content || '<span class="text-slate-500">No log entries yet.</span>';
                } else {
                    document.getElementById('auditLogContent').textContent = 'Failed to load audit log: ' + (result.error || 'Unknown error');
                }
            } catch (err) {
                document.getElementById('auditLogContent').textContent = 'Failed to load audit log: ' + err.message;
            }
        }
        
        async function clearAuditLog() {
            if (!confirm('Are you sure you want to clear the audit log? A backup will be created.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'clear_audit_log');
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Audit log cleared (backup created)');
                    loadAuditLog();
                } else {
                    showToast(result.error || 'Failed to clear audit log', 'error');
                }
            } catch (err) {
                showToast('Failed to clear audit log', 'error');
            }
        }
        
        // Change Password Functions
        function showChangePasswordModal() {
            showModal('changePasswordModal');
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        }
        
        async function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                showToast('Please fill in all fields', 'error');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showToast('New passwords do not match', 'error');
                return;
            }
            
            if (newPassword.length < 8) {
                showToast('Password must be at least 8 characters', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'change_password');
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            formData.append('confirm_password', confirmPassword);
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Password changed successfully!');
                    hideModal('changePasswordModal');
                } else {
                    showToast(result.error || 'Failed to change password', 'error');
                }
            } catch (err) {
                showToast('Failed to change password', 'error');
            }
        }
    </script>
</body>
</html>