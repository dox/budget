<?php
function timeAgoFromSeconds($seconds) {
	if ($seconds <= 0) {
		return 'just now';
	}

	// Define time units
	$units = [
		'year' => 31536000,   // 365 days
		'month' => 2592000,   // 30 days
		'day' => 86400,       // 1 day
		'hour' => 3600,       // 1 hour
		'minute' => 60,       // 1 minute
		'second' => 1         // 1 second
	];

	foreach ($units as $unit => $value) {
		if ($seconds >= $value) {
			$count = floor($seconds / $value);
			return $count . ' ' . $unit . ($count > 1 ? 's' : '') . ' ago';
		}
	}
}

function printArray($data): void {
	echo "<div class=\"alert alert-info\" role=\"alert\" style=\"font-family:monospace;\"><pre>";
	
	if (is_array($data) || is_object($data)) {
		echo htmlspecialchars(print_r($data, true));
	} else {
		echo htmlspecialchars(var_export($data, true));
	}
	echo "</pre></div>";
}

function formatMoney(int|float $amount): string {
	$currencySymbol = "£";
	
	// Ensure proper rounding and thousands separator
	return $currencySymbol . number_format($amount, 2, '.', ',');
}