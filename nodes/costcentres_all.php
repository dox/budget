<?php
$cost_centre_class = new class_cost_centres;

$groups = $cost_centre_class->groups();

if (isset($_POST['code'])) {
	$data = Array (
		"code" => $_POST['code'],
		"name" => $_POST['name'],
		"department" => $_POST['department'],
		"description" => $_POST['description'],
		"grouping" => $_POST['grouping'],
		"colour" => $_POST['colour'],
		"value" => $_POST['budget']
	);

	$cost_centre_class->insert($data);

	$title = "Cost Centre Created";
	$message = "New cost centre '" . $_POST['name'] . "' created";
	echo toast($title, $message);
}

$cost_centres = $cost_centre_class->all();
?>

<h2>Cost Centres <small class="text-muted"><?php echo "Budget Year: " . budgetStartDate() . " - " . budgetEndDate(); ?></small></h2>

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col" style="width: 50px;"></th>
			<th scope="col" style="width: 120px;">Code</th>
			<th scope="col">Name</th>
			<th scope="col">Budget</th>
			<th scope="col">Remaining Budget</th>
			<th scope="col" style="width: 70px;"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($groups AS $group) {
			$output  = "<tr><td colspan='6'>" . ($group) ."</td></tr>";

			foreach ($cost_centres AS $cost_centre) {
				$output .= "";
				$remainingValue = $cost_centre['value'] - $cost_centre_class->totalSpendByCostCentre($cost_centre['uid']);

				if ($cost_centre['grouping'] == $group) {
					if ($cost_centre['value'] <= 0) {
						$remainingValuePercentage = 0;
					} else {
						$remainingValuePercentage = round(($remainingValue / $cost_centre['value'])*100);
					}

					// if the budget is spent, make it red
					// if the budget is less than the current percentage way through the year, make it yellow
					// otherwise, make it blue
					if ($remainingValuePercentage <= 0) {
						if ($remainingValue < 0) {
							$progressBarClass = "bg-danger";
						} else {
							$progressBarClass = "bg-info";
						}
						$remainingValuePercentage = 100;

					} elseif ($remainingValuePercentage < (100-percentageIntoBudget())) {
						$progressBarClass = "bg-warning";
					} else {
						$progressBarClass = "bg-info";
					}

					$output .= "<td scope=\"row\"><svg width=\"16\" height=\"16\" style=\"color: " . $cost_centre['colour'] . ";\"><use xlink:href=\"img/icons.svg#archive-fill\"/></svg></td>";
					$output .= "<td><a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\">" . $cost_centre['code'] . "</td>";
					$output .= "<td>" . $cost_centre['name'] . "</td>";
					$output .= "<td>£" . number_format($cost_centre['value']) . "</td>";
					$output .= "<td><div class=\"progress\" ><div class=\"progress-bar " . $progressBarClass . "\" role=\"progressbar\" style=\"width: " . $remainingValuePercentage . "%;\" aria-valuenow=\"" . $remainingValuePercentage . "\" aria-valuemin=\"0\" aria-valuemax=\"100\">£" . number_format($remainingValue) . " </div></div>" . "</td>";
					$output .= "<td>" . "<a href=\"index.php?n=costcentres_edit&uid=" . $cost_centre['uid'] . "\"><i class=\"fas fa-pencil-alt\"></i></a> ";
					$output .= "<a href=\"index.php?n=costcentres_delete&uid=" . $cost_centre['uid'] . "\"><i class=\"fas fa-trash-alt\"></i></a>" . "</td>";
					$output .= "</tr>";
				}

			}
			echo $output;
		}
		?>

	</tbody>
</table>
