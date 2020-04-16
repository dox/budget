<?php
class class_users {

public function getOne($usernameOrUID = null) {
	global $db;
	
	$user = $db->where("username", $usernameOrUID);
	$user = $db->orWhere ("uid", $usernameOrUID);
	$user = $db->getOne("users");
	
	return $user;
}

public function all() {
	global $db;
	
	$users = $db->orderBy('username', "DESC");
	$users = $db->get("users");
	
	return $meters;
}

public function displayMeterCard($uid = null) {
	$this->meterUID = $uid;
	$meter = $this->getOne();
	
	if ($this->daysSinceLastUpdate($meter['uid']) > 80) {
		$lastUpdated = "<span class=\"badge badge-danger\">Updated " . $this->daysSinceLastUpdate($meter['uid']) . " days ago</span>";
	} else if ($this->daysSinceLastUpdate($meter['uid']) > 40) {
		$lastUpdated = "<span class=\"badge badge-warning\">Updated " . $this->daysSinceLastUpdate($meter['uid']) . " days ago</span>";
	} else if ($this->daysSinceLastUpdate($meter['uid']) < 30) {
		$lastUpdated = "<span class=\"badge badge-primary\">Updated " . $this->daysSinceLastUpdate($meter['uid']) . " days ago</span>";
	}
				
	$output  = "<div class=\"col-md-4\">";
		$output .= "<div class=\"card mb-4 shadow-sm\" >";
			if (isset($meter['photograph'])) {
				$img = "uploads/" . $meter['photograph'];
				$output .= "<img class=\"bd-placeholder-img card-img-top\" src=\"" . $img . "\" width=\"100%\" height=\"225\">";
			} else {
				$output .= "<svg class=\"bd-placeholder-img card-img-top\" width=\"100%\" height=\"225\" xmlns=\"http://www.w3.org/2000/svg\" preserveAspectRatio=\"xMidYMid slice\" focusable=\"false\" role=\"img\" \"><rect width=\"100%\" height=\"100%\" fill=\"#55595c\"/></svg>";
			}
			$output .= "<div class=\"card-img-overlay\">" . $lastUpdated . "</div>";
			$output .= "<div class=\"card-body\">";
				$output .= "<h5 class=\"card-title\">" . $meter['name'] . "</h5>";
				$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
					$output .= "<div class=\"btn-group\">";
						$output .= "<a href=\"index.php?n=meter&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
						$output .= "<a href=\"index.php?n=meter_edit&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
					$output .= "</div>";
					$output .= "<small class=\"text-muted\">" . $meter['type'] . " / <samp>" . $meter['serial'] . "</samp></small>";
				$output .= "</div>";
				
				
			$output .= "</div>";	
		$output .= "</div>";
	$output .= "</div>";
	
	return $output;
}
} //end CLASS
?>