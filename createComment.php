<?php
	require "../../cgi-bin/mysqlcred.php";
	$name = $_POST['name'];
	$id = $_POST['requestId'];
	$text = $_POST['text'];

	$sql = "INSERT INTO `eresgaf_comment`(`requestId`, `creatorName`, `content`) VALUES ($id, '$name', '$text')";
	mysqli_query($link, $sql);
?>