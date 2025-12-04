<?php
/**
 * Quick Diagnostic Script for HTTP 500 Error
 * Upload this to your public_html or Laravel root and access via browser
 * URL: https://app.kobopoint.com/diagnose-500.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>KoboPoint 500 Error Diagnostic</h1>";
echo "<pre>";

// 1. Check PHP Version
echo "1. PHP Version: " . PHP_VERSION . "\n";
echo "   Required: >= 8.2\n";
echo "   Status: " . (version_compare(PHP_VERSION, '8.2.0', '>=') ? "✅ OK" : "❌ TOO OLD") . "\n\n";

// 2. Check Required Extensions
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'curl'];
echo "2. PHP Extensions:\n";
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    echo "   $ext: " . ($loaded ? "✅ Loaded" : "❌ MISSING") . "\n";
}
echo "\n";

// 3. Check .env file
echo "3. Environment File:\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "   ✅ .env exists\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($envPath)), -4) . "\n";
    $envContent = file_get_contents($envPath);
    echo "   APP_ENV: " . (preg_match('/APP_ENV=(.+)/', $envContent, $m) ? trim($m[1]) : 'NOT FOUND') . "\n";
    echo "   APP_DEBUG: " . (preg_match('/APP_DEBUG=(.+)/', $envContent, $m) ? trim($m[1]) : 'NOT FOUND') . "\n";
    echo "   DB_HOST: " . (preg_match('/DB_HOST=(.+)/', $envContent, $m) ? trim($m[1]) : 'NOT FOUND') . "\n";
} else {
    echo "   ❌ .env NOT FOUND at: $envPath\n";
}
echo "\n";

// 4. Check vendor directory
echo "4. Vendor Directory:\n";
$vendorPath = __DIR__ . '/vendor';
if (is_dir($vendorPath)) {
    echo "   ✅ vendor/ exists\n";
    $autoload = $vendorPath . '/autoload.php';
    echo "   autoload.php: " . (file_exists($autoload) ? "✅ Exists" : "❌ MISSING") . "\n";
} else {
    echo "   ❌ vendor/ NOT FOUND - Run: composer install\n";
}
echo "\n";

// 5. Check storage permissions
echo "5. Storage Directory:\n";
$storagePath = __DIR__ . '/storage';
if (is_dir($storagePath)) {
    echo "   ✅ storage/ exists\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "\n";
    echo "   Writable: " . (is_writable($storagePath) ? "✅ Yes" : "❌ NO - Fix with: chmod -R 755 storage") . "\n";
    
    $logsPath = $storagePath . '/logs';
    if (is_dir($logsPath)) {
        echo "   logs/ exists: ✅\n";
        $logFile = $logsPath . '/laravel.log';
        if (file_exists($logFile)) {
            echo "   laravel.log exists: ✅\n";
            echo "   Last 5 lines of error log:\n";
            $lines = file($logFile);
            $lastLines = array_slice($lines, -5);
            foreach ($lastLines as $line) {
                echo "      " . htmlspecialchars($line) . "\n";
            }
        }
    }
} else {
    echo "   ❌ storage/ NOT FOUND\n";
}
echo "\n";

// 6. Check bootstrap/cache
echo "6. Bootstrap Cache:\n";
$bootstrapCache = __DIR__ . '/bootstrap/cache';
if (is_dir($bootstrapCache)) {
    echo "   ✅ bootstrap/cache/ exists\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($bootstrapCache)), -4) . "\n";
    echo "   Writable: " . (is_writable($bootstrapCache) ? "✅ Yes" : "❌ NO - Fix with: chmod -R 755 bootstrap/cache") . "\n";
} else {
    echo "   ❌ bootstrap/cache/ NOT FOUND\n";
}
echo "\n";

// 7. Test Database Connection
echo "7. Database Connection Test:\n";
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    preg_match('/DB_HOST=(.+)/', $envContent, $dbHost);
    preg_match('/DB_DATABASE=(.+)/', $envContent, $dbName);
    preg_match('/DB_USERNAME=(.+)/', $envContent, $dbUser);
    preg_match('/DB_PASSWORD=(.+)/', $envContent, $dbPass);
    
    $host = isset($dbHost[1]) ? trim($dbHost[1]) : 'localhost';
    $database = isset($dbName[1]) ? trim($dbName[1]) : '';
    $username = isset($dbUser[1]) ? trim($dbUser[1]) : '';
    $password = isset($dbPass[1]) ? trim($dbPass[1]) : '';
    
    if ($database && $username) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            echo "   ✅ Database connection successful\n";
        } catch (PDOException $e) {
            echo "   ❌ Database connection FAILED\n";
            echo "   Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ⚠️  Database credentials not found in .env\n";
    }
} else {
    echo "   ⚠️  Cannot test - .env not found\n";
}
echo "\n";

// 8. Check public/index.php
echo "8. Public Index File:\n";
$indexPath = __DIR__ . '/public/index.php';
if (file_exists($indexPath)) {
    echo "   ✅ public/index.php exists\n";
} else {
    echo "   ❌ public/index.php NOT FOUND\n";
    echo "   If using cPanel, you may need to adjust paths\n";
}
echo "\n";

// 9. Check .htaccess
echo "9. .htaccess File:\n";
$htaccessPath = __DIR__ . '/public/.htaccess';
if (file_exists($htaccessPath)) {
    echo "   ✅ public/.htaccess exists\n";
} else {
    echo "   ❌ public/.htaccess NOT FOUND\n";
}
echo "\n";

// 10. Check file structure
echo "10. Directory Structure:\n";
$dirs = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'vendor'];
foreach ($dirs as $dir) {
    $exists = is_dir(__DIR__ . '/' . $dir);
    echo "   $dir/: " . ($exists ? "✅" : "❌ MISSING") . "\n";
}

echo "\n";
echo "=== END OF DIAGNOSTIC ===\n";
echo "</pre>";

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Fix any ❌ errors shown above</li>";
echo "<li>Check storage/logs/laravel.log for detailed error</li>";
echo "<li>Ensure vendor/ directory exists (run: composer install)</li>";
echo "<li>Set proper permissions: chmod -R 755 storage bootstrap/cache</li>";
echo "<li>Clear caches: php artisan config:clear</li>";
echo "</ol>";

