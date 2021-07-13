<?php
$orders_class = new class_orders;
$orders = $orders_class->all();

$YTDTotalSpend = 0;

foreach ($orders AS $order) {
	$userObject = new user($order['username']);

	$username = "'" . $userObject->firstname . " " . $userObject->lastname . "'";

	if (isset($usersArray[$username])) {
		$usersArray[$username] = $usersArray[$username] + $order['value'];
	} else {
		$usersArray[$username] = $order['value'];
	}

	$YTDTotalSpend = $YTDTotalSpend + $order['value'];
}

arsort($usersArray);
?>

<canvas id="myChart" width="400" height="200"></canvas>

<script>
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(", ", array_keys($usersArray));?>],
        datasets: [{
            label: '£',
            data: [<?php echo implode(",", $usersArray); ?>]
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>

<hr />

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col">User</th>
			<th scope="col" style="width: 200px;">Total Spend</th>
			<th scope="col" style="width: 80px;">%</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($usersArray AS $user => $value) {
			$output  = "<tr>";
			$output .= "<td scope=\"row\">" . str_replace("'", "", $user)  . "</td>";
			$output .= "<td>£" . number_format($value,2) . "</td>";
			$output .= "<td>" . number_format(($value/$YTDTotalSpend)*100,1) . "</td>";

			$output .= "</tr>";

			echo $output;
		}
		?>

	</tbody>
</table>
