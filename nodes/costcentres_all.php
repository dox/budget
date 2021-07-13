<?php
$cost_centre_class = new class_cost_centres;


if (isset($_POST['code'])) {
	$costCentre = new cost_centre();
	$data = Array (
		"code" => $_POST['code'],
		"name" => $_POST['name'],
		"department" => $_POST['department'],
		"description" => $_POST['description'],
		"grouping" => $_POST['grouping'],
		"colour" => $_POST['colour'],
		"value" => $_POST['budget']
	);

	$costCentre->create($data);

	$title = "Cost Centre Created";
	$message = "New cost centre '" . $_POST['name'] . "' created";
	echo toast($title, $message);
}

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
		foreach($cost_centre_class->groups() AS $group) {
			$output  = "<tr><td colspan='6'>" . ($group['grouping']) ."</td></tr>";

			foreach ($cost_centre_class->all() AS $cost_centre) {
				$cost_centre = new cost_centre($cost_centre['uid']);

				$output .= "";
				if ($cost_centre->grouping == $group['grouping']) {
					if ($cost_centre->yearlyBudget() <= 0) {
						$remainingValuePercentage = 0;
					} else {
						$remainingValuePercentage = round(($cost_centre->yearlyRemaining() / $cost_centre->yearlyBudget())*100);
					}

					// if the budget is spent, make it red
					// if the budget is less than the current percentage way through the year, make it yellow
					// otherwise, make it blue
					if ($remainingValuePercentage <= 0) {
						if ($cost_centre->yearlyRemaining() < 0) {
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

					$output .= "<td scope=\"row\"><svg width=\"16\" height=\"16\" style=\"color: " . $cost_centre->colour . ";\"><use xlink:href=\"img/icons.svg#archive-fill\"/></svg></td>";
					$output .= "<td><a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre->uid . "\">" . $cost_centre->code . "</td>";
					$output .= "<td>" . $cost_centre->name . "</td>";
					$output .= "<td>£" . number_format($cost_centre->yearlyBudget()) . "</td>";
					$output .= "<td><div class=\"progress\" ><div class=\"progress-bar " . $progressBarClass . "\" role=\"progressbar\" style=\"width: " . $remainingValuePercentage . "%;\" aria-valuenow=\"" . $remainingValuePercentage . "\" aria-valuemin=\"0\" aria-valuemax=\"100\">£" . number_format($cost_centre->yearlyRemaining()) . " </div></div>" . "</td>";
					$output .= "<td>" . "<a href=\"index.php?n=costcentres_edit&uid=" . $cost_centre->uid . "\"><svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#pencil-square\"/></svg></a> ";
					$output .= "<a href=\"index.php?n=costcentres_delete&uid=" . $cost_centre->uid . "\"><svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#trash\"/></svg></a>" . "</td>";
					$output .= "</tr>";
				}

			}
			echo $output;
		}
		?>

	</tbody>
</table>
