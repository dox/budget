<?php
$orders = $orders->allThisYear();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Orders</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=orders_new" class="btn btn-sm btn-outline-secondary">New</a>
		</div>
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="bi bi-calendar3" aria-hidden="true"></i>
				This year
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
				<th scope="col">#</th>
				<th scope="col">Header</th>
				<th scope="col">Header</th>
				<th scope="col">Header</th>
				<th scope="col">Header</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($orders AS $order) {
				$output  = "<tr>";
				$output .= "<td>" . $order->uid . "</td>";
				$output .= "<td>" . $order->costCenter() . "</td>";
				$output .= "";
				$output .= "";
				$output .= "";
				$output .= "";
				$output .= "</tr>";
				
				echo $output;
			}
			?>
		</tbody>
	</table>
</div>