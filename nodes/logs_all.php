<?php
$logs_class = new class_logs;
$logs_class->purge();
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

<canvas id="canvas" width="400" height="100"></canvas>
<form class="form-inline">
  <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Quick Filter</label>
  <input type="text" class="form-control mb-2 mr-sm-2 flex-fill" id="logSearchInput" placeholder="Filter Logs...">
</form>
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
	<tbody id="logsTable">
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

<?php
$sql = "SELECT DATE(date) AS date, count(*) AS logsTotal
		FROM logs
		GROUP BY DATE(date)
		ORDER BY DATE(date) DESC;";
$logsTotal = $db->rawQuery($sql);

foreach ($logsTotal AS $log) {
	$logsArray["'" . $log['date'] . "'"] = "'" . $log['logsTotal'] . "'";
}
?>

<script>
	var timeFormat = 'YYYY/MM/DD';

	var config = {
		type: 'line',
		data: {
			labels: [<?php echo implode(", ", array_keys($logsArray));?>],
			datasets: [{
				label: 'Logs',
				data: [<?php echo implode(",", $logsArray); ?>],
			}]
		},
		options: {
			title: {
				text: 'Logs'
			},
			elements: {
				line: {
					tension: 0
				}
			},
			legend: {
				display: false
			},
			scales: {
				xAxes: [{
					type: 'time',
					time: {
						parser: timeFormat,
						// round: 'day'
						tooltipFormat: 'll'
					},
					scaleLabel: {
						display: false
					}
				}],
				yAxes: [{
					ticks: {
						suggestedMin: 0
					},
					scaleLabel: {
						display: false,
						labelString: 'Total Logs'
					}
				}]
			},
		}
	};

	window.onload = function() {
		var ctx = document.getElementById('canvas').getContext('2d');
		window.myLine = new Chart(ctx, config);
	};

$(document).ready(function(){
  $("#logSearchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#logsTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
