<?php
session_start();

require_once __DIR__ . '/global.php';

$legacyConfigPath = __DIR__ . '/config.php';
$sampleConfigPath = __DIR__ . '/config.SAMPLE.php';

if (file_exists($legacyConfigPath)) {
	require_once $legacyConfigPath;
} elseif (file_exists($sampleConfigPath)) {
	require_once $sampleConfigPath;
}

if (!defined('APP_NAME')) {
	define('APP_NAME', 'Budget');
}

if (!defined('APP_DEBUG')) {
	define('APP_DEBUG', false);
}

if (!defined('DB_HOST') && isset($db_host)) {
	define('DB_HOST', $db_host);
}

if (!defined('DB_NAME') && isset($db_name)) {
	define('DB_NAME', $db_name);
}

if (!defined('DB_USER') && isset($db_username)) {
	define('DB_USER', $db_username);
}

if (!defined('DB_PASS') && isset($db_password)) {
	define('DB_PASS', $db_password);
}

if (!defined('LDAP_HOST') && defined('LDAP_SERVER')) {
	define('LDAP_HOST', LDAP_SERVER);
}

if (!defined('LDAP_BIND_USER') && defined('LDAP_BIND_DN')) {
	define('LDAP_BIND_USER', LDAP_BIND_DN);
}

if (!defined('LDAP_BIND_PASS') && defined('LDAP_BIND_PASSWORD')) {
	define('LDAP_BIND_PASS', LDAP_BIND_PASSWORD);
}

# ------------------------------------------------------------
# 2. Set debugging
# ------------------------------------------------------------
if (APP_DEBUG) {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	set_error_handler(function ($errno, $errstr, $errfile, $errline) {
		echo "<div class=\"alert alert-danger\" role=\"alert\">";
		echo "<strong>PHP ERROR:</strong> [$errno] $errstr<br>";
		echo "In <strong>$errfile</strong> on line <strong>$errline</strong>";
		echo "</div>";
		return false;
	});

	set_exception_handler(function ($e) {
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "<strong>UNCAUGHT EXCEPTION:</strong> " . get_class($e) . "<br>";
		echo $e->getMessage() . "<br><br>" . $e->getTraceAsString();
		echo "</div>";
	});
} else {
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	error_reporting(0);

	ini_set('log_errors', '1');
	ini_set('error_log', __DIR__ . '/php-error.log');
}

# ------------------------------------------------------------
# 3. Register class autoloader
# ------------------------------------------------------------
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Model.php';
require_once __DIR__ . '/../classes/BudgetYear.php';
require_once __DIR__ . '/../classes/CostCentre.php';
require_once __DIR__ . '/../classes/Orders.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/User.php';

# ------------------------------------------------------------
# 4. Initialise shared Database instance
# ------------------------------------------------------------
try {
	global $db;
	$db = Database::getInstance();
} catch (Throwable $e) {
	error_log("Database connection failed: " . $e->getMessage());
	die('<h1>Database connection error: ' . htmlspecialchars($e->getMessage()) . '</h1>');
}

$year = (int)date('Y');
if (new DateTime() < new DateTime("$year-08-01")) {
	$year--;
}

$log = new Log();
$budgetyear = new BudgetYear($year);
$user = new User();
$settings = new Settings();
