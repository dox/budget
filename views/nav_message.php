<?php
$navBarMessagesClass = new class_navbar_messages;
$navBarMessages = $navBarMessagesClass->all();	

foreach ($navBarMessages AS $message) {
	if ($message['type'] == "success") {
		$colour = "#6cc758";
	} elseif ($message['type'] == "error") {
		$colour = "#F86380";
	} else {
		$colour = "#63f8db"; // light blue
		//$colour = "#e1e0e0"; // light grey
	}
	echo "<div class=\"text-center\" style=\"padding: 15px; color: #FFF; background: ". $colour . ";\">" . $message['message'] . "</div>";
	
	if ($message['persistent'] == 0) {
		$db->where('uid', $message['uid']);
		$db->delete('navbar_messages');
	}
}
?>