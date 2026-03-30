<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Logs</h1>
</div>
<?php
$logs = $log->getRecent();
?>

<table class="table table-striped-columns">
	<thead>
		<tr>
			<th>Date</th>
			<th>Type</th>
			<th>Username</th>
			<th>IP Address</th>
			<th>Event</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($logs as $row) {
			if ($row['type'] == "INFO") {
				$typeBadgeClass = "text-bg-info";
			} elseif ($row['type'] == "WARNING") {
				$typeBadgeClass = "text-bg-warning";
			} elseif ($row['type'] == "ERROR") {
				$typeBadgeClass = "text-bg-danger";
			} elseif ($row['type'] == "DEBUG") {
				$typeBadgeClass = "text-bg-primary";
			} else {
				$typeBadgeClass = "text-bg-secondary";
			}
			
			$typeBadge = "<span class=\"badge rounded-pill " . $typeBadgeClass . "\">" . strtoupper($row['type']) . "</span>";
			$output  = "<tr>";
			$output .= "<td>" . $row['date_created'] . "</td>";
			$output .= "<td>" . $typeBadge . "</td>";
			$output .= "<td>" . $row['username'] . "</td>";
			$output .= "<td>" . $row['ip'] . "</td>";
			
			if (isset($row['event'])) {
				$event = nl2br(htmlspecialchars($row['event']));
			} else {
				$event = "";
			}
			$output .= "<td>" . $event . "</td>";
			$output .= "</tr>";
			
			echo $output;
			
		}
		?>
			
	</tbody>
</table>