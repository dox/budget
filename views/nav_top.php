<div class="container">
	<nav class="navbar navbar-expand navbar-light">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active"><a class="nav-link" href="index.php?n=dashboard"><i class="far fa-home fa-sm color-blue"></i> Dashboard</a></li>
				<li class="nav-item"><a class="nav-link" href="index.php?n=orders_all"><i class="far fa-exchange-alt fa-sm color-green"></i> Orders</a></li>
				<li class="nav-item"><a class="nav-link" href="index.php?n=costcentres_all"><i class="far fa-coins fa-sm color-red"></i> Cost Centres</a></li>
				<li class="nav-item"><a class="nav-link" href="index.php?n=reports_all"><i class="far fa-chart-line fa-sm color-blue"></i> Reports</a></li>
			</ul>
			<div class="btn-group btn-group-sm float-right">
				<button onclick="window.location.href = 'index.php?n=orders_create';" type="button" class="btn btn-primary">Create Order</button>
				<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="index.php?n=costcentres_create"><i class="far fa-coins"></i> Create Cost Centre</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="#"><i class="far fa-file-upload"></i> Create Import</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="index.php?n=user_settings"><i class="far fa-user"></i> User Settings</a>
					<a class="dropdown-item" href="index.php?n=logs_all"><i class="far fa-clock"></i> Logs</a>
				</div>
			</div>
			<!--<form class="form-inline my-2 my-lg-0">
				<input class="form-control mr-sm-2 " type="search" placeholder="Search" aria-label="Search">
			</form>-->
		</div>
	</nav>
</div>
