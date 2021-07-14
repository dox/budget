<?php
$log->purge();

$user = new user($_SESSION['username']);

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
  <input type="text" class="form-control mb-2 mr-sm-2 flex-fill" id="logs_fiter_input" onkeyup="tableFilter()" placeholder="Filter Logs...">
</form>
<table class="table bg-white" id="logsTable">
	<thead>
		<tr class="header">
			<th scope="col" style="width: 190px;">Date</th>
			<th scope="col">Type</th>
			<th scope="col">Description</th>
			<th scope="col">IP</th>
			<th scope="col">Username</th>
		</tr>
	</thead>
	<tbody id="logsTable">
		<?php
		foreach ($log->all($limit = 1000) AS $logEntry) {
			if ($user->type == "administrator") {
				$date = date('Y-m-d H:i:s', strtotime($logEntry['date']));
				$type = $logEntry['type'];
				$description = $logEntry['description'];
				$ip = $logEntry['ip'];
				$username = $logEntry['username'];

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
			$output .= "<td scope=\"row\">" . $date . "</td>";
			$output .= "<td>" . $type . "</td>";
			$output .= "<td>" . $description . "</td>";
			$output .= "<td>" . $ip . "</td>";
			$output .= "<td>" . $username . "</td>";
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


<script>
	var timeFormat = 'YYYY/MM/DD';

	var config = {
		type: 'line',
		data: {
			labels: [<?php echo implode(", ", array_keys($log->summary()));?>],
			datasets: [{
				label: 'Logs',
				data: [<?php echo implode(",", $log->summary()); ?>],
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


function tableFilter() {
  const input = document.getElementById("logs_fiter_input");
  const inputStr = input.value.toUpperCase();
  document.querySelectorAll('#logsTable tr:not(.header)').forEach((tr) => {
    const anyMatch = [...tr.children]
      .some(td => td.textContent.toUpperCase().includes(inputStr));
    if (anyMatch) tr.style.removeProperty('display');
    else tr.style.display = 'none';
  });
}
</script>
