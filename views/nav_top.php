<nav class="navbar navbar-expand navbar-light">
	<div class="container">

			<ul class="navbar-nav mr-auto">
				<li class="nav-item active"><a class="nav-link" href="index.php?n=dashboard"><span class="d-none d-sm-block"><i class="fas fa-home fa-sm color-blue"></i> Dashboard</span><span class="d-block d-sm-none"><i class="fas fa-home fa-lg color-blue"></i></span></a></li>
				<li class="nav-item"><a class="nav-link" href="index.php?n=orders_all"><span class="d-none d-sm-block"><i class="fas fa-exchange-alt fa-sm color-green"></i> Orders</span><span class="d-block d-sm-none"><i class="fas fa-exchange-alt fa-lg color-green"></i></span></a></li>
				<li class="nav-item"><a class="nav-link" href="index.php?n=costcentres_all"><span class="d-none d-sm-block"><i class="fas fa-coins fa-sm color-red"></i> Cost Centres</span><span class="d-block d-sm-none"><i class="fas fa-coins fa-lg color-red"></i></span></a></li>
				<li class="nav-item"><a class="nav-link" href="index.php?n=reports_all"><span class="d-none d-sm-block"><i class="fas fa-chart-line fa-sm color-blue"></i> Reports</span><span class="d-block d-sm-none"><i class="fas fa-chart-line fa-lg color-blue"></i></span></a></li>
			</ul>
			<form class="form-inline my-2 my-lg-0 d-none d-sm-block" method="POST" action="index.php?n=search">
				<input class="form-control mr-sm-2 " type="search" name="search" placeholder="Search" aria-label="Search">
			</form>
			<div class="btn-group btn-group-sm float-right">
				<button onclick="window.location.href = 'index.php?n=orders_create';" type="button" class="btn btn-primary">Create Order</button>
				<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="index.php?n=search"><i class="fas fa-search"></i> Search</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="index.php?n=costcentres_create"><i class="fas fa-coins"></i> Create Cost Centre</a>
					<a class="dropdown-item" href="#"><i class="fas fa-file-upload"></i> Create Import</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="index.php?n=user_settings"><i class="fas fa-user"></i> User Settings</a>
					<a class="dropdown-item" href="index.php?n=logs_all"><i class="fas fa-clock"></i> Logs</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="index.php?n=index&logout"><i class="fas fa-sign-out-alt"></i></i> Sign Out</a>
				</div>
			</div>
	</div>
</nav>
