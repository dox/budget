<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db_host		=	"localhost";
$db_name		=	"database-name";	
$db_username	=	"database-username";
$db_password	=	"database-password";

define("LDAP_SERVER", "ldap-server");
define("LDAP_BASE_DN", "DC=some,DC=ldap,DC=base,DC=dn");
define("LDAP_ACCOUNT_SUFFIX", "@some.ldap.account.suffix");

define("BUDGET_STARTDATE", "2019-08-01");
define("BUDGET_ENDDATE", "2020-07-31");

function daysIntoBudget() {
	$timediff = time() - strtotime(BUDGET_STARTDATE);
	$datediff = round($timediff / (60 * 60 * 24));
	
	return ($datediff);
}

function percentageIntoBudget() {
	$totalDaysThisYear = cal_days_in_year(date('Y'));
	$daysIntoBudget = daysIntoBudget();
	
	$percentagediff = round(($daysIntoBudget / $totalDaysThisYear) * 100);
	return ($percentagediff);
}

function cal_days_in_year($year) {
	$days=0; 
	for($month=1;$month<=12;$month++){
		$days = $days + cal_days_in_month(CAL_GREGORIAN,$month,$year);
	}
	
	return $days;
}
?>

