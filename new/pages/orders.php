<?php
$orders = new Orders();
$ordersAll = $orders->allThisYear();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Orders</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=order_addedit&action=add" class="btn btn-sm btn-outline-secondary"><i class="bi bi-plus-circle" aria-hidden="true"></i> New</a>
		</div>
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="bi bi-calendar3" aria-hidden="true"></i> This year
			</button>
			<ul class="dropdown-menu">
				<li><a class="dropdown-item" href="#">Next year</a></li>
				<li><a class="dropdown-item" href="#">This Year</a></li>
				<li><a class="dropdown-item" href="#">Last Year</a></li>
			</ul>
		</div>
	</div>
</div>

<canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>

<h2>Orders (<?php echo $budgetyear->getStart()->format('Y-m-d') . " to " . $budgetyear->getEnd()->format('Y-m-d'); ?>)</h2>
<div class="table-responsive small">
	
	<table class="table table-striped table-sm">
		<thead>
			<tr>
				<th scope="col">Date</th>
				<th scope="col">Cost Centre</th>
				<th scope="col">Name</th>
				<th scope="col">Value</th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($ordersAll AS $order) {
				$costCentreURL = "index.php?page=order&id=" . $order->id;
				$orderURL = "index.php?page=order&id=" . $order->id;
				
				$output  = "<tr>";
				$output .= "<td>" . date("Y-m-d H:i", strtotime($order->date_created)) . "</td>";
				$output .= "<td><a href=\"" . $costCentreURL . "\">" . $order->costCentre() . "</a></td>";
				$output .= "<td><a href=\"" . $orderURL . "\"><strong>" . $order->po . "</strong> " . $order->name . "</a></td>";
				$output .= "<td>" . formatMoney($order->value) . "</td>";
				$output .= "<td>
					<div class=\"action-icons\">
						<a href=\"index.php?page=order_addedit&action=edit&id=" . $order->id . "\"><i class=\"bi bi-pencil\"></i></a>
						<a href=\"index.php?page=order_addedit&action=clone&id=" . $order->id . "\"><i class=\"bi bi-copy\"></i></a>
					</div>
				</td>";
				$output .= "";
				$output .= "</tr>";
				
				echo $output;
			}
			?>
		</tbody>
	</table>
</div>

<script>
// ==== Data ====
const data = {
  labels: ['January', 'February', 'March', 'April', 'May'],
  datasets: [
	{
	  label: 'Apples',
	  data: [12, 19, 3, 5, 2],
	  backgroundColor: '#007bff'
	},
	{
	  label: 'Bananas',
	  data: [2, 3, 20, 5, 1],
	  backgroundColor: '#ffc107'
	},
	{
	  label: 'Cherries',
	  data: [3, 10, 13, 15, 22],
	  backgroundColor: '#dc3545'
	}
  ]
};

// ==== Config ====
const config = {
  type: 'bar',
  data: data,
  options: {
	plugins: {
	  title: {
		display: false,
		text: 'Fruit Sales (Stacked Bar)'
	  },
	  legend: {
		position: 'top'
	  }
	},
	responsive: true,
	scales: {
	  x: {
		stacked: true
	  },
	  y: {
		stacked: true,
		beginAtZero: true
	  }
	}
  }
};

// ==== Render ====
new Chart(
  document.getElementById('myChart'),
  config
);
</script>