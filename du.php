<?php
/**
 * cPanel Directory Usage Analyzer - Single File Distribution
 * 
 * A PHP-based web tool that provides disk usage analysis for cPanel hosting accounts.
 * Simply upload this single file to any directory in your cPanel account and access via browser.
 * 
 * @version 2.0.0
 * @author Damian West <damian@damian.id.au>
 * @license MIT
 */

// ============================================================================
// CONFIGURATION
// ============================================================================

// Performance settings
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

// Application settings
define('APP_NAME', 'cPanel Directory Usage Analyzer');
define('APP_VERSION', '2.0.0');
define('MAX_DEPTH', 100); // Prevent infinite recursion
define('APP_PASSWORD', ''); // Change this password for security

// ============================================================================
// PASSWORD PROTECTION
// ============================================================================

session_start();

// Security check for empty password
if (empty(APP_PASSWORD) || APP_PASSWORD === '') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= APP_NAME ?> - Setup</title>
        <style>
            body {
                font-family: 'Courier New', monospace;
                margin: 0;
                padding: 0;
                background: #f5f5f5;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .security-container {
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                max-width: 500px;
                width: 90%;
                text-align: center;
            }
            .security-header {
                margin-bottom: 30px;
            }
            .security-header h1 {
                color: #333;
                margin: 0 0 10px 0;
                font-size: 1.8em;
            }
            .security-header p {
                color: #666;
                margin: 0;
                font-size: 1em;
            }
            .warning-content {
                text-align: left;
                background: #e9ecef;
                padding: 20px;
                border-radius: 5px;
                margin: 20px 0;
            }
            .warning-content h3 {
                color: #495057;
                margin-top: 0;
                font-size: 1.1em;
            }
            .code-block {
                background: #f8f9fa;
                padding: 12px;
                border-radius: 4px;
                font-family: 'Courier New', monospace;
                font-size: 0.9em;
                border: 1px solid #dee2e6;
                margin: 10px 0;
            }
            .step-list {
                list-style: decimal;
                padding-left: 20px;
            }
            .step-list li {
                margin-bottom: 10px;
            }
            .footer-note {
                margin-top: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 5px;
                font-size: 0.9em;
                color: #6c757d;
            }
        </style>
    </head>
    <body>
        <div class="security-container">
            <div class="security-header">
                <h1>⚙️ Setup Required</h1>
                <p>Please configure a password to continue</p>
            </div>
            
            <div class="warning-content">
                <h3>Configuration Needed</h3>
                <p>A password needs to be set in the configuration file before this tool can be used.</p>
            </div>
            
            <div style="text-align: left;">
                <h3>Instructions:</h3>
                <ol class="step-list">
                    <li>Edit the <code><?= basename(__FILE__) ?></code> file</li>
                    <li>Find: <div class="code-block">define('APP_PASSWORD', '');</div></li>
                    <li>Change to: <div class="code-block">define('APP_PASSWORD', 'your_password');</div></li>
                    <li>Save and refresh this page</li>
                </ol>
            </div>
            
            <div class="footer-note">
                Choose a password you'll remember. This prevents unauthorized access to your directory listings.
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Check if password is required
if (!empty(APP_PASSWORD)) {
    // Check if user is already authenticated
    if (!isset($_SESSION['du_authenticated']) || $_SESSION['du_authenticated'] !== true) {
        // Check if password is being submitted
        if (isset($_POST['password'])) {
            if ($_POST['password'] === APP_PASSWORD) {
                $_SESSION['du_authenticated'] = true;
                // Redirect to avoid POST resubmission
                header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_GET['path']) ? '?path=' . urlencode($_GET['path']) : ''));
                exit;
            } else {
                $password_error = 'Incorrect password. Please try again.';
            }
        }
        
        // Show password form if not authenticated
        if (!isset($_SESSION['du_authenticated']) || $_SESSION['du_authenticated'] !== true) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?= APP_NAME ?> - Login</title>
                <style>
                    body {
                        font-family: 'Courier New', monospace;
                        margin: 0;
                        padding: 0;
                        background: #6c757d;
                        min-height: 100vh;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .login-container {
                        background: white;
                        padding: 40px;
                        border-radius: 10px;
                        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
                        max-width: 400px;
                        width: 90%;
                        text-align: center;
                    }
                    .login-header {
                        margin-bottom: 30px;
                    }
                    .login-header h1 {
                        color: #333;
                        margin: 0 0 10px 0;
                        font-size: 1.8em;
                    }
                    .login-header p {
                        color: #666;
                        margin: 0;
                        font-size: 0.9em;
                    }
                    .form-group {
                        margin-bottom: 20px;
                        text-align: left;
                    }
                    label {
                        display: block;
                        margin-bottom: 5px;
                        color: #333;
                        font-weight: bold;
                        font-size: 0.9em;
                    }
                    input[type="password"] {
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #ddd;
                        border-radius: 5px;
                        font-family: 'Courier New', monospace;
                        font-size: 1em;
                        box-sizing: border-box;
                    }
                    input[type="password"]:focus {
                        outline: none;
                        border-color: #667eea;
                    }
                    .login-btn {
                        background: #495057;
                        color: white;
                        padding: 12px 30px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        font-family: 'Courier New', monospace;
                        font-size: 1em;
                        font-weight: bold;
                        width: 100%;
                        transition: transform 0.2s;
                    }
                    .login-btn:hover {
                        transform: translateY(-2px);
                    }
                    .error {
                        background: #f8d7da;
                        color: #721c24;
                        padding: 10px;
                        border-radius: 5px;
                        margin-bottom: 20px;
                        font-size: 0.9em;
                    }
                    .security-note {
                        margin-top: 25px;
                        padding: 15px;
                        background: #f8f9fa;
                        border-radius: 5px;
                        font-size: 0.8em;
                        color: #666;
                        text-align: left;
                    }
                </style>
            </head>
            <body>
                <div class="login-container">
                    <div class="login-header">
                        <h1>🔐 <?= APP_NAME ?></h1>
                        <p>Please enter the password to access the disk usage analyzer</p>
                        <?php if (isset($_SERVER['HTTP_HOST'])): ?>
                            <p style="font-size: 0.8em; opacity: 0.7; margin-top: 10px;">Running on: <?= htmlspecialchars($_SERVER['HTTP_HOST']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($password_error)): ?>
                        <div class="error">
                            <?= htmlspecialchars($password_error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required autofocus>
                        </div>
                        
                        <button type="submit" class="login-btn">Access Analyzer</button>
                    </form>
                    
                    <div class="security-note">
                        <strong>🛡️ Security Note:</strong><br>
                        This tool provides access to your entire cPanel directory structure. 
                        The password protects against unauthorized access to your file and folder information.
                    </div>
                </div>
            </body>
            </html>
            <?php
            exit;
        }
    }
}

// ============================================================================
// PATH HANDLER CLASS
// ============================================================================

class PathHandler {
    private $baseDir;
    
    public function __construct() {
        // Start from parent directory to scan entire cPanel structure
        $this->baseDir = dirname(dirname(__FILE__)); // Parent of current directory
    }
    
    public function getBaseDir() {
        return $this->baseDir;
    }
    
    public function sanitizePath($path) {
        // Remove dangerous path traversal attempts
        $path = str_replace(['..\\', '..\\\\'], '', $path);
        $path = ltrim($path, '/\\');
        
        // Handle relative paths starting with ../
        if (strpos($path, '../') === 0) {
            $path = substr($path, 3);
        }
        
        return $path;
    }
    
    public function getFullPath($relativePath) {
        $relativePath = $this->sanitizePath($relativePath);
        $fullPath = $this->baseDir . ($relativePath ? DIRECTORY_SEPARATOR . $relativePath : '');
        
        // Ensure we're still within the base directory
        if (!is_dir($fullPath) || strpos(realpath($fullPath), realpath($this->baseDir)) !== 0) {
            return $this->baseDir;
        }
        
        return $fullPath;
    }
    
    public function getParentPath($currentPath) {
        if (!$currentPath) return '';
        return dirname($currentPath);
    }
    
    public function buildBreadcrumb($currentPath) {
        $breadcrumb = [];
        if ($currentPath) {
            $pathParts = explode('/', $currentPath);
            $cumulativePath = '';
            foreach ($pathParts as $part) {
                $cumulativePath .= ($cumulativePath ? '/' : '') . $part;
                $breadcrumb[] = [
                    'name' => $part,
                    'path' => $cumulativePath
                ];
            }
        }
        return $breadcrumb;
    }
}

// ============================================================================
// DIRECTORY SCANNER CLASS
// ============================================================================

class DirectoryScanner {
    
    public function calculateDirectorySize($directory, $depth = 0) {
        if ($depth > MAX_DEPTH) return 0;
        
        $size = 0;
        try {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($files as $file) {
                if ($file->isFile() && !$file->isLink()) {
                    $size += $file->getSize();
                }
            }
        } catch (Exception $e) {
            // Handle permission errors gracefully
            return 0;
        }
        
        return $size;
    }
    
    public function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    public function scanDirectory($path) {
        $contents = [];
        
        if (!is_readable($path)) {
            return $contents;
        }
        
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $fullItemPath = $path . DIRECTORY_SEPARATOR . $item;
            
            if (is_link($fullItemPath)) {
                // Handle symlinks - don't count their size
                $contents[] = [
                    'name' => $item,
                    'type' => 'symlink',
                    'size' => 0,
                    'formatted_size' => 'Symlink',
                    'modified' => filemtime($fullItemPath),
                    'target' => readlink($fullItemPath)
                ];
            } elseif (is_dir($fullItemPath)) {
                try {
                    $size = $this->calculateDirectorySize($fullItemPath);
                    $contents[] = [
                        'name' => $item,
                        'type' => 'directory',
                        'size' => $size,
                        'formatted_size' => $this->formatBytes($size),
                        'modified' => filemtime($fullItemPath)
                    ];
                } catch (Exception $e) {
                    $contents[] = [
                        'name' => $item,
                        'type' => 'directory',
                        'size' => 0,
                        'formatted_size' => 'Error',
                        'modified' => filemtime($fullItemPath)
                    ];
                }
            } else {
                $size = filesize($fullItemPath);
                $contents[] = [
                    'name' => $item,
                    'type' => 'file',
                    'size' => $size,
                    'formatted_size' => $this->formatBytes($size),
                    'modified' => filemtime($fullItemPath)
                ];
            }
        }
        
        // Sort by size (largest first)
        usort($contents, function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        return $contents;
    }
    
    public function calculateTotalSize($contents) {
        $totalSize = 0;
        foreach ($contents as $item) {
            if ($item['type'] !== 'symlink') {
                $totalSize += $item['size'];
            }
        }
        return $totalSize;
    }
}

// ============================================================================
// MAIN APPLICATION LOGIC
// ============================================================================

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$pathHandler = new PathHandler();
$scanner = new DirectoryScanner();

// Get current path from URL parameter
$currentPath = isset($_GET['path']) ? $_GET['path'] : '';
$currentPath = $pathHandler->sanitizePath($currentPath);

// Get full path and ensure it's valid
$fullPath = $pathHandler->getFullPath($currentPath);

// Scan directory contents
$contents = $scanner->scanDirectory($fullPath);
$totalSize = $scanner->calculateTotalSize($contents);
$totalSizeFormatted = $scanner->formatBytes($totalSize);
$itemCount = count($contents);

// Build navigation data
$breadcrumb = $pathHandler->buildBreadcrumb($currentPath);
$parentPath = $pathHandler->getParentPath($currentPath);

// ============================================================================
// HTML TEMPLATE
// ============================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #333;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
        }
        .version {
            font-size: 12px;
            opacity: 0.8;
            float: right;
        }
        .breadcrumb {
            background: #e9ecef;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .summary {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .directory {
            color: #007bff;
            font-weight: bold;
        }
        .file {
            color: #6c757d;
        }
        .symlink {
            color: #adb5bd;
            font-style: italic;
            opacity: 0.7;
        }
        .size {
            text-align: right;
            font-family: monospace;
        }
        .icon {
            width: 20px;
            display: inline-block;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        a:hover {
            text-decoration: underline;
        }
        .loading {
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #6c757d;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            body { margin: 10px; }
            .container { padding: 15px; }
            th, td { padding: 8px; }
            .header h1 { font-size: 1.5em; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="version">
                v<?= APP_VERSION ?>
                <?php if (!empty(APP_PASSWORD)): ?>
                    | <a href="?logout=1" style="color: #ccc; text-decoration: none;" 
                         onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
                <?php endif; ?>
            </div>
            <h1>📁 <?= APP_NAME ?></h1>
            <p>Single-file PHP disk usage analyzer for cPanel hosting accounts</p>
            <?php if (isset($_SERVER['HTTP_HOST'])): ?>
                <p style="font-size: 0.8em; opacity: 0.7;">Running on: <?= htmlspecialchars($_SERVER['HTTP_HOST']) ?></p>
            <?php endif; ?>
        </div>

        <div class="breadcrumb">
            <strong>Current Path:</strong> 
            <a href="?path=">cPanel Root</a>
            <?php foreach ($breadcrumb as $crumb): ?>
                / <a href="?path=<?= urlencode($crumb['path']) ?>"><?= htmlspecialchars($crumb['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="summary">
            <strong>Total Size:</strong> <?= $totalSizeFormatted ?> 
            <strong>Items:</strong> <?= $itemCount ?>
            <strong>Last Updated:</strong> <?= date('Y-m-d H:i:s') ?>
        </div>

        <?php if (empty($contents)): ?>
            <div class="loading">
                Directory is empty or not accessible.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th class="size">Size</th>
                        <th>Last Modified</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($currentPath): ?>
                        <tr>
                            <td>
                                <a href="?path=<?= urlencode($parentPath) ?>">
                                    <span class="icon">↩️</span> <strong>.. (Parent Directory)</strong>
                                </a>
                            </td>
                            <td>directory</td>
                            <td class="size">-</td>
                            <td>-</td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($contents as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['type'] === 'directory'): ?>
                                    <a href="?path=<?= urlencode($currentPath . ($currentPath ? '/' : '') . $item['name']) ?>" class="directory">
                                        <span class="icon">📁</span> <?= htmlspecialchars($item['name']) ?>
                                    </a>
                                <?php elseif ($item['type'] === 'symlink'): ?>
                                    <span class="symlink">
                                        <span class="icon">🔗</span> <?= htmlspecialchars($item['name']) ?>
                                        <small style="font-size: 11px; color: #999;"> → <?= htmlspecialchars($item['target']) ?></small>
                                    </span>
                                <?php else: ?>
                                    <span class="file">
                                        <span class="icon">📄</span> <?= htmlspecialchars($item['name']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= $item['type'] ?></td>
                            <td class="size"><?= $item['formatted_size'] ?></td>
                            <td><?= date('Y-m-d H:i:s', $item['modified']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="footer">
            <div style="text-align: center; padding: 15px; border-top: 1px solid #eee; color: #6c757d;">
                <small>
                    <?= APP_NAME ?> v<?= APP_VERSION ?> | 
                    Generated by GitHub Copilot | 
                    <a href="https://github.com/damiankw/cdua/" target="_blank" style="color: #007bff;">Open Source</a>
                </small>
            </div>
        </div>
    </div>
</body>
</html>