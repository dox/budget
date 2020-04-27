<?php
session_start();

require_once('../inc/config.php');
require_once('../inc/global_functions.php');
require_once('../database/MysqliDb.php');
require_once('../inc/adLDAP/adLDAP.php');
require_once('../inc/logs.php');
require_once('../inc/departments.php');
require_once('../inc/cost_centres.php');
require_once('../inc/orders.php');
require_once('../inc/users.php');
require_once('../inc/suppliers.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_POST['departmentUID'])) {
	$log->insert("logon", "Temporary department change (" . $_SESSION['department'] . "->" . $_POST['departmentUID'] . ") successful for " . $_SESSION['username']);

	$title = "Temporary Department Change";
	$message = "You have temporarily changed departments";
	echo toast($title, $message);

	$_SESSION['department'] = $_POST['departmentUID'];
}
?>
