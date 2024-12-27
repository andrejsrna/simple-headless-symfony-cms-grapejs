<?php
echo "<h1>Environment Check</h1>";
echo "<pre>";

// Check document root
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";

// Check if index.php exists
echo "Index.php exists: " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/index.php') ? 'Yes' : 'No') . "\n";

// Check permissions
echo "Directory permissions: " . substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'])), -4) . "\n";

// Check PHP version and modules
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded Extensions: \n" . implode("\n", get_loaded_extensions()) . "\n";

// Check Symfony environment
echo "APP_ENV: " . getenv('APP_ENV') . "\n";
echo "APP_DEBUG: " . getenv('APP_DEBUG') . "\n";

// Check var directory permissions
$varDir = dirname($_SERVER['DOCUMENT_ROOT']) . '/var';
echo "Var directory exists: " . (is_dir($varDir) ? 'Yes' : 'No') . "\n";
echo "Var directory writable: " . (is_writable($varDir) ? 'Yes' : 'No') . "\n";

echo "</pre>"; 