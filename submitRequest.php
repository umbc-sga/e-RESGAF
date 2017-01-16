<?php include "includes/header.php";?>
<div class="main-content center"> 
	<br>

	<div class="subtitle">
		New RESGAF submitted <br><br>
		<a href="newrequest.php" class="aButton navButton greenButton">Submit another Request</a>
	</div>
<br><br><br><br><br><br>
<?php
	require "../../cgi-bin/mysqlcred.php";
	$sql = 'INSERT INTO `eresgaf_request`(`organization`, `creator`, `email`';
	$vals = '' . '"' . $_POST['organization'] . '" ';
	$vals .= ', "' . $_POST['creator'] . '", "' . $_POST['email'] . '"';

	$sql .= ', `phone`, `eventType`';
	$vals .= ', "' . $_POST['phone'] . '", ';
	if($_POST['type'] == "event"){
		$vals .= 1;
		$sql .= ', `eventName`, `eventDateTime`';
		$vals .= ', "' . $_POST['eventName'] . '", "'. date("Y-m-d", strtotime($_POST['eventDate'])) . ' ';
		$vals .= '' . date("H:i:s", strtotime($_POST['eventTime'])) . '"';
	}else{
		$vals .= 0;
		$sql .= ', `expenditureDescription`';
		$vals .= ', "' . $_POST['description'] . '"';
	}

	$sql .=') VALUES (' . $vals . ')';
	mysqli_query($link, $sql);

	$numLines = $_POST['numLines'];
	$reqId = 0;
	$sql = 'SELECT `id` FROM `eresgaf_request` WHERE `organization` = "';
	$sql .= $_POST['organization'] . '" AND `creator`=' . '"' . $_POST['creator'] . '" ORDER BY date DESC';
	$results = mysqli_query($link, $sql);
	if($row = mysqli_fetch_assoc($results)){
		$reqId = (int)($row['id']);
	}

	//INSERT INTO `eresgaf_lineItem`(`requestId`, `descrption`, `venderName`, `cost`, `sgaAllocation`, `clubAccount`, `phone`, `address`, `finssn`, `contactPerson`) VALUES ()
	for($i = 1; $i <= $numLines; $i++){
		$sql = 'INSERT INTO `eresgaf_lineItem`(`requestId`, `descrption`, `venderName`, `cost`, `sgaAllocation`, `clubAccount`';
		$vals = '' . $reqId . ', "' . $_POST['descriptionLine' . $i] . '", "' . $_POST['vender' . $i] .'"';
		$vals .= ', ' . ($_POST['SGAall' . $i] + $_POST['clubacc' . $i]) . ', ' . $_POST['SGAall' . $i];
		$vals .= ', ' . $_POST['clubacc' . $i];

		if(strlen($_POST['phone' . $i]) > 0){
			$sql .= ', `phone`';
			$vals .= ', "' . $_POST['phone' . $i] . '"';
		}

		if(strlen($_POST['address' . $i]) > 0){
			$sql .= ', `address` ';
			$vals .= ', "' . $_POST['address' . $i] . '"';
		}

		if(strlen($_POST['finssn' . $i]) > 0){
			$sql .= ', `finssn`';
			$vals .= ', "' . $_POST['finssn' . $i] . '"';
		}

		if(strlen($_POST['contact' . $i]) > 0){
			$sql .= ', `contactPerson`';
			$vals .= ', "' . $_POST['contact' . $i] . '"';
		}
		$sql .=') VALUES (' . $vals . ')';
		mysqli_query($link, $sql);
	}

?>

</div>
<script type="text/javascript">
	window.onload = function(){
		document.getElementById("navNew").className += "navcurrent";
	}
</script>

<?php include "includes/footer.php";?>