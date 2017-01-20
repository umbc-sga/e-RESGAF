<?php
	require "../../cgi-bin/mysqlcred.php";
	require "sendEmail.php";

	$name = $_POST['name'];
	$id = $_POST['requestId'];
	$text = $_POST['text'];

	$sql = "INSERT INTO `eresgaf_comment`(`requestId`, `creatorName`, `content`) VALUES ($id, '$name', '$text')";
	mysqli_query($link, $sql);

	$sql = "SELECT * FROM `eresgaf_request` WHERE `id`= " . $id;
	$results = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($results);
	if($row['email'] == $_SERVER['mail']){
		newComment($_SERVER['mail'], $row['organization'], $row['email'], $row['id']);
	}
?>