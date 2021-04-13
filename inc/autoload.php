<?php
session_start();

$root = $_SERVER['DOCUMENT_ROOT'];

require_once($root . '/inc/config.php');

if (debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

require $root . '/vendor/autoload.php';

use LdapRecord\Connection;

// Create a new connection:
$ldap_connection = new Connection([
    'hosts' => [LDAP_SERVER],
    'port' => LDAP_PORT,
    'base_dn' => LDAP_BASE_DN,
    'username' => LDAP_BIND_DN,
		'password' => LDAP_BIND_PASSWORD,
		'use_tls' => LDAP_STARTTLS,
]);
try {
    $ldap_connection->connect();
} catch (\LdapRecord\Auth\BindException $e) {
    $error = $e->getDetailedError();

    echo $error->getErrorCode();
    echo $error->getErrorMessage();
    echo $error->getDiagnosticMessage();
}

require_once($root . '/inc/global_functions.php');
require_once($root . '/database/MysqliDb.php');
//require_once($root . '/inc/adLDAP/adLDAP.php');
require_once($root . '/inc/logs.php');
require_once($root . '/inc/departments.php');
require_once($root . '/inc/cost_centres.php');
require_once($root . '/inc/orders.php');
require_once($root . '/inc/users.php');
require_once($root . '/inc/suppliers.php');
require_once($root . '/inc/uploads.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);
?>
