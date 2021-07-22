<?php
require_once('inc/autoload.php');

if (isset($_GET['logout']) && isset($_SESSION['username'])) {
	$log->insert("logon", "Logout successful for " . $_SESSION['username']);

	$_SESSION = array();
	session_destroy();
	unset($_COOKIE['SEHbudget']);

	$title = "Log Out complete";
	$message = "You have been logged out";
	echo toast($title, $message);
}

if (isset($_POST['loginformsubmit'])) { //prevent null bind
	if ($ldap_connection->auth()->attempt($_POST['username'] . LDAP_ACCOUNT_SUFFIX, $_POST['password'], $stayAuthenticated = true)) {
    // Successfully authenticated user.

		$users_class = new class_users;
		$user = new user($_POST['username']);

		if ($user) {
			$_SESSION['logged_on'] = true;
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['department'] = $user->department;
			$_SESSION['localUID'] = $user->uid;
			//$_SESSION['userinfo'] = $adldap->user()->info($username);

			if ($_POST['remember-me'] == "remember-me") {
				setcookie("seh_budget_username", $_POST['username'], time()+3600);
			}
			setcookie("SEHbudget", $_POST['username'], time()+3600);
			$log->insert("logon", "Logon successful for " . $_POST['username']);
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?n=dashboard";
			header($redir);
		} else {
			$log->insert("logon", "LDAP Logon successful for " . $_POST['username'] . " but resource access denied");
			$title = "Access Denied";
			$message = "You do not have access to this resource (although your username/password was correct";
			echo toast($title, $message);

			session_destroy();

			$redir = "Location: http://budget.seh.ox.ac.uk/index.php?n=login";
			header($redir);
		}
	} else {
    // Username or password is incorrect.
		$log->insert("logon", "Logon failed for " . $_POST['username']);

		$title = "Access Denied";
		$message = "Incorrect username/password supplied";
		echo toast($title, $message);

		session_destroy();

		$message = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Warning!</strong> Login attempt failed.</div>";
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Dashboard - Budget</title>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<meta name="csrf-token" content="ZZxLKKzw7Jk0G1KVLSUXm7tNcF9eMLxH9nqH5LxW"/>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="css/app.css"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

	<link rel="apple-touch-icon" sizes="57x57" href="/ico/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/ico/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/ico/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/ico/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/ico/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/ico/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/ico/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/ico/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/ico/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/ico/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/ico/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/ico/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/ico/favicon-16x16.png">
	<link rel="manifest" href="/ico/manifest.json">
	<meta name="msapplication-TileColor" content="#f77b7c">
	<meta name="msapplication-TileImage" content="/ico/ms-icon-144x144.png">
	<meta name="theme-color" content="#f77b7c">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/moment@2.27.0"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@0.1.1"></script>
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script src="js/app.js"></script>
</head>
<body>
	<header>
	<?php
	if (isset($_SESSION['logged_on']) && $_SESSION['logged_on'] == true) {
		include('views/nav_top.php');
	}
	?>
	</header>
	<br />
	<main>
	<main class="container">
		<?php
		if (isset($_SESSION['username'])) {
			if (isset($_GET['n'])) {
				$node = "nodes/" . $_GET['n'] . ".php";

				if (!file_exists($node)) {
					$node = "nodes/404.php";
				}
			} else {
				$node = "nodes/login.php";
			}
		} else {
			$node = "nodes/login.php";
		}

		include_once($node);
		?>
	</main>
	<?php
	if (isset($_SESSION['username'])) {
		include('views/footer.php');
	}
	?>
</body>

</html>
