<?php
/**
 * Application bootstrap and autoloader
 * 
 * - Loads configuration
 * - Registers the class autoloader
 * - Creates a shared Database instance
 */

session_start();

# ------------------------------------------------------------
# 1. Load configuration
# ------------------------------------------------------------
$config = include(__DIR__ . '/../config/config.php');

# ------------------------------------------------------------
# 2. Register class autoloader
# ------------------------------------------------------------
spl_autoload_register(function ($class) {
	$baseDir = __DIR__ . '/../classes/';
	$file = $baseDir . $class . '.php';
	if (file_exists($file)) {
		require_once $file;
	} else {
		error_log("Autoloader: could not load class {$class} ({$file})");
	}
});

$log = new Log();
$budgetyear = new BudgetYear();
$user = new User();
$orders = new Orders();

# ------------------------------------------------------------
# 3. Initialise shared Database instance
# ------------------------------------------------------------
try {
	$db = Database::getInstance();
} catch (Throwable $e) {
	// Handle connection errors gracefully
	error_log("Database connection failed: " . $e->getMessage());
	die('<h1>Database connection error: ' . $e->getMessage() . '</h1>');
}