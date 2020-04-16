<?php
$logs_class = new class_logs;
$logs = $logs_class->all();	

$user_class = new class_users;
$user = $user_class->getOne($_SESSION['username']);

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>

<h2>Logs</h2>

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col" style="width: 190px;">Date</th>
			<th scope="col">Type</th>
			<th scope="col">Description</th>
			<th scope="col">IP</th>
			<th scope="col">Username</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($logs AS $log) {
			if ($user['type'] == "administrator") {
				$date = date('Y-m-d H:i:s', strtotime($log['date']));
				$type = $log['type'];
				$description = $log['description'];
				$ip = $log['ip'];
				$username = $log['username'];
				
				$class = "";
			} else {
				$date = date('Y-m-d H:i:s');
				$type = generateRandomString(rand(4,7));
				$description = generateRandomString(rand(30,55));
				$ip = generateRandomString(rand(13,15));
				$username = generateRandomString(rand(6,12));
				
				$class = "blurry";
			}
			
			$output  = "<tr class=\"" . $class . "\">";
			$output .= "<th scope=\"row\">" . $date . "</th>";
			$output .= "<th>" . $type . "</th>";
			$output .= "<th>" . $description . "</th>";
			$output .= "<th>" . $ip . "</th>";
			$output .= "<th>" . $username . "</th>";
			$output .= "</tr>";
			
			echo $output;
			
			
		}
		?>
		
	</tbody>
</table>

<style>
.blurry {
	color: transparent;
	text-shadow: 0 0 7px rgba(0,0,0,0.9);
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	-o-user-select: none;
	user-select: none;
}
</style>