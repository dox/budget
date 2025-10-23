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
		<?php foreach ($logs as $row): ?>
			<tr>
				<td><?= htmlspecialchars($row['date_created']) ?></td>
				<td><?= htmlspecialchars($row['type']) ?></td>
				<td><?= htmlspecialchars($row['username'] ?? '') ?></td>
				<td><?= htmlspecialchars($row['ip']) ?></td>
				<td><?= nl2br(htmlspecialchars($row['event'])) ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>