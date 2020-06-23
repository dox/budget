<?php
session_start();

require_once('inc/config.php');
require_once('inc/global_functions.php');
require_once('database/MysqliDb.php');
require_once('inc/adLDAP/adLDAP.php');
require_once('inc/logs.php');
require_once('inc/departments.php');
require_once('inc/cost_centres.php');
require_once('inc/orders.php');
require_once('inc/users.php');
require_once('inc/suppliers.php');
require_once('inc/uploads.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_GET['logout']) && isset($_SESSION['username'])) {
	$log->insert("logon", "Logout successful for " . $_SESSION['username']);

	$_SESSION = array();
	session_destroy();
	unset($_COOKIE['SEHbudget']);

	$title = "Log Out complete";
	$message = "You have been logged out";
	echo toast($title, $message);
}

//you should look into using PECL filter or some form of filtering here for POST variables
if (isset($_POST['username'])) {
	$username = strtoupper($_POST['username']); //remove case sensitivity on the username
	$password = $_POST['password'];
}

if (isset($_POST['loginformsubmit'])) { //prevent null bind
	if ($username != NULL && $password != NULL){
        try {
		    $adldap = new adLDAP();
        }
        catch (adLDAPException $e) {
            echo $e;
            exit();
        }

		//authenticate the user
		if ($adldap->authenticate($username, $password)){
			//establish your session and redirect

			$users_class = new class_users;
			$user = $users_class->getOne($username);

			if ($user) {
				$_SESSION['username'] = $username;
				$_SESSION['department'] = $user['department'];
				$_SESSION['localUID'] = $user['uid'];
				$_SESSION['userinfo'] = $adldap->user()->info($username);

				if ($_POST['remember-me'] == "remember-me") {
					setcookie("seh_budget_username", $_SESSION['username'], time()+3600);
				}
				setcookie("SEHbudget", $_SESSION['username'], time()+3600);
				$log->insert("logon", "Logon successful for " . $username);
				$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?n=dashboard";
				header($redir);
			} else {
				$log->insert("logon", "LDAP Logon successful for " . $_SESSION['username'] . " but resource access denied");
				$title = "Access Denied";
				$message = "You do not have access to this resource (although your username/password was correct";
				echo toast($title, $message);

				$redir = "Location: http://budget.seh.ox.ac.uk/index.php?n=login";
				header($redir);

			}

			exit;
		} else {
			$log->insert("logon", "Logon failed for " . $_POST['username']);

			$title = "Access Denied";
			$message = "Incorrect username/password supplied";
			echo toast($title, $message);
		}
	}

	$message = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Warning!</strong> Login attempt failed.</div>";
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Dashboard - Budget</title>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<meta name="csrf-token" content="ZZxLKKzw7Jk0G1KVLSUXm7tNcF9eMLxH9nqH5LxW"/>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:400,400i,600,600i"/>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<link rel="stylesheet" href="css/app.css"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />

	<link rel="apple-touch-icon" sizes="57x57" href="ico/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="ico/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="ico/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="ico/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="ico/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="ico/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="ico/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="ico/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="ico/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="ico/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="ico/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="ico/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="ico/favicon-16x16.png">
    <link rel="shortcut icon" href="ico/favicon.ico" type="image/x-icon">

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js" integrity="sha256-TQq84xX6vkwR0Qs1qH5ADkP+MvH0W+9E7TdHJsoIQiM=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
		<script src="js/app.js"></script>
</head>
<body>
	<?php include('views/nav_top.php');?>
	<?php include('views/nav_message.php');?>
	<br />
	<div class="container">
		<?php
		if (isset($_SESSION['username'])) {
			if (isset($_GET['n'])) {
				$node = "nodes/" . $_GET['n'] . ".php";

				if (!file_exists($node)) {
					$node = "nodes/404.php";
				}
			} else {
				$node = "nodes/404.php";
			}
		} else {
			$node = "nodes/login.php";
		}

		include_once($node);
		?>
	</div>
	<?php include('views/footer.php');?>
</body>

</html>
