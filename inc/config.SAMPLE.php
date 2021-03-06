<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db_host		=	"localhost";
$db_name		=	"database-name";
$db_username	=	"database-username";
$db_password	=	"database-password";

define("UPLOAD_DIR", "/your/upload/path/here/");

define("RESET_URL", "https://www.example.com");

define("LDAP_SERVER", "ldap-server");
define("LDAP_BASE_DN", "DC=some,DC=ldap,DC=base,DC=dn");
define("LDAP_ACCOUNT_SUFFIX", "@some.ldap.account.suffix");

define("BUDGET_STARTDATE", "08-01"); // MONTH/DAY: 08/01
define("BUDGET_ENDDATE", "07-31"); // MONTH/DAY: 07/31

define("smtp_server", "smtp.example.com");
define("smtp_sender_address", "noreply@example.com");
define("smtp_sender_name", "Budget System");
?>
