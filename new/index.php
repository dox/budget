<!DOCTYPE html>
<?php
include_once('inc/autoload.php');

if (!$user->isLoggedIn()) {
	header("Location: login.php");
}
?>
<html lang="en" data-bs-theme="auto">
<head>
	<?php include_once('layout/html_head.php'); ?>
</head>
<body>
	<?php include_once('layout/header.php'); ?>
	
	<div class="container-fluid">
		<div class="row">
			<?php include('layout/sidebar.php'); ?>
			
			<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
				<?php
				// Determine which page to show
				$page = $_GET['page'] ?? 'test';
				$pageFile = "pages/{$page}.php";
				if (!file_exists($pageFile)) $pageFile = 'pages/404.php';
				
				include($pageFile);
				?>
			</main>
		</div>
	</div>
	
	<?php include_once('layout/footer.php'); ?>
	
	
	
	<script>
		/* globals Chart:false */
		
		(() => {
		  'use strict'
		
		  // Graphs
		  const ctx = document.getElementById('myChart')
		  new Chart(ctx, {
			type: 'line',
			data: {
			  labels: [
				'Sunday',
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday'
			  ],
			  datasets: [{
				data: [
				  15339,
				  21345,
				  18483,
				  24003,
				  23489,
				  24092,
				  12034
				],
				lineTension: 0,
				backgroundColor: 'transparent',
				borderColor: '#007bff',
				borderWidth: 4,
				pointBackgroundColor: '#007bff'
			  }]
			},
			options: {
			  plugins: {
				legend: {
				  display: false
				},
				tooltip: {
				  boxPadding: 3
				}
			  }
			}
		  })
		})()
	</script>
</body>
</html>
