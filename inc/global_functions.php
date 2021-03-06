<?php
function daysIntoBudget() {
	$timediff = time() - strtotime(budgetStartDate());
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

	$output  = "<div class=\"toast\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\" data-delay=\"5000\">";
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

function tidyUrl($url) {
	// trim the string
	$url = trim($url);

	// check for a schema and if there isn't one then add it
	if(substr($url,0,5)!='https' && substr($url,0,4)!='http' && substr($url,0,3)!='ftp'){
		$url = 'http://'.$url;
	};

	//  parse the url
	$parsed = @parse_url($url);
	if(!is_array($parsed)){
		return false;
	}

	// rebuild url
	$url = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
	$url .= isset($parsed['user']) ? $parsed['user'].(isset($parsed['pass']) ? ':'.$parsed['pass'] : '').'@' : '';
	$url .= isset($parsed['host']) ? $parsed['host'] : '';
	$url .= isset($parsed['port']) ? ':'.$parsed['port'] : '';

	// if no path exists then add a slash
	if(isset($parsed['path'])){
		$url .= (substr($parsed['path'],0,1) == '/') ?   $parsed['path'] : ('/'.$parsed['path']);
	} else {
		$url .= '/';
	};

	// append query
	$url .= isset($parsed['query']) ? '?'.$parsed['query'] : '';

	// return url string
	return $url;
}

function sendMail($subject = "No Subject Specified", $recipients = NULL, $body = NULL, $senderAddress = NULL, $senderName = NULL) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/PHPMailer/class.phpmailer.php');

	$mail = new PHPMailer;

	$mail->IsSMTP();
	$mail->Host = smtp_server;
	$mail->IsHTML(true);

	if (isset($senderAddress)) {
		$mail->From = $senderAddress;
		$mail->FromName = $senderName;
		$mail->AddReplyTo($senderAddress, $senderName);
	} else {
		$mail->From = "communications@seh.ox.ac.uk";
		$mail->FromName = "SEH Communications";
		$mail->AddReplyTo("communications@seh.ox.ac.uk", "SEH Communications");

	}

	//$recipients = explode(",", $recipients);

	//echo $recipients;

	//$mail->AddAddress("noreply@seh.ox.ac.uk");
	foreach ($recipients AS $recipient) {
		$mail->addBCC($recipient);
	}




	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->IsHTML(true);                                  // Set email format to HTML

	$mail->Subject = $subject;
	$mail->Body    = $body;
	//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if($mail->Send()) {
		//$logInsert = (new Logs)->insert("email","success",null,"Email message sent to " . implode(", ",$recipients) . " <code>Subject: " . $subject . "</code>");
	} else {
		//$logInsert = (new Logs)->insert("email","error",null,"Email message could not be sent to " . implode(", ",$recipients) . " <code>" . $mail->ErrorInfo . "</code>");
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		exit;
	}

	//echo 'Message has been sent';
}
?>
