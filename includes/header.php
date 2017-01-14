<?php
	require "../../cgi-bin/mysqlcred.php";
	$sql = "SELECT * FROM `eresgaf_privilegedUser` WHERE `email` = '" . $_SERVER['mail'] . "'";
	$results = mysqli_query($link, $sql);
	$approve = False;
	if($row = mysqli_fetch_assoc($results)){
		$approve = True;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>UMBC SGA RESGAF</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
<div id="title">UMBC SGA RESGAF</div>
<nav>
	<a href="/e-resgaf"><div class="navtab " id = "navHame">
		 Home
	</div></a>
	<a href="newrequest.php"><div class="navtab " id = "navNew">
		New Request
	</div></a>
	<a href="viewrequests.php"><div class="navtab " id = "navView">
		View Requests
	</div></a>
	<a href="approverequests.php"><div class="navtab <?php if(!$approve){echo 'hide';}?>" id = "navApprove">
		Approve Requests
	</div></a>
	<div class="user navtab">
		<?php echo $_SERVER['givenName'];?>
	</div>
</nav>

