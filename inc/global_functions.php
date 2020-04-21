<?php
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

function toast($title = null, $message = null) {
	if ($title == null) {
		$title = "UNKNOWN";
	}
	
	if ($message == null) {
		$message = "UNKNOWN";
	}
	
	$output  = "<div class=\"toast\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">";
		$output .= "<div class=\"toast-header\">";
			$output .= "<i class=\"far fa-bell\"></i>";
			$output .= "<strong class=\"mr-auto\">" . $title . "</strong>";
			$output .= "<small>Just now</small>";
			$output .= "<button type=\"button\" class=\"ml-2 mb-1 close\" data-dismiss=\"toast\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
		$output .= "</div>";
		$output .= "<div class=\"toast-body\">";
			$output .= $message;
		$output .= "</div>";
	$output .= "</div>";
	
	//$output = "TEST";
	return $output;
}
?>