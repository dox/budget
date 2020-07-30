<?php
session_start();

$root = $_SERVER['DOCUMENT_ROOT'];

require_once($root . '/inc/config.php');
require_once($root . '/inc/global_functions.php');
require_once($root . '/database/MysqliDb.php');
require_once($root . '/inc/adLDAP/adLDAP.php');
require_once($root . '/inc/logs.php');
require_once($root . '/inc/departments.php');
require_once($root . '/inc/cost_centres.php');
require_once($root . '/inc/orders.php');
require_once($root . '/inc/users.php');
require_once($root . '/inc/suppliers.php');
require_once($root . '/inc/uploads.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);
?>
