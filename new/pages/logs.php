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
				<td><?= htmlspecialchars($row['date']) ?></td>
				<td><?= htmlspecialchars($row['type']) ?></td>
				<td><?= htmlspecialchars($row['username'] ?? '') ?></td>
				<td><?= htmlspecialchars($row['ip']) ?></td>
				<td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>