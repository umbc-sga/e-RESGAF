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
	$resId = 0;
	if(isset($_POST['reqId'])){
		$sql = 'UPDATE `eresgaf_request` SET `organization`= "' . $_POST['organization'];
		$sql .='",`email`="' . $_POST['email'] . '",`phone`="'. $_POST['phone'];
		$sql .='",`budgetItem`="' . $_POST['budgetItem'] . '" ,`eventType`=';
		if($_POST['type'] == "event"){
			$sql .= '1, `eventName`="' . $_POST['eventName'] . '", `eventDateTime= "'. date("Y-m-d", strtotime($_POST['eventDate'])) . ' ' . date("H:i:s", strtotime($_POST['eventTime'])) . '"';
		}else{
			$sql .= '0, `expenditureDescription`="' . $_POST['description'] . '"';
		}
		$sql .=' WHERE `id` =' . $_POST['resId'];
		mysqli_query($link, $sql);
		$reqId = (int)($_POST['reqId']);
		$sql = 'DELETE FROM `eresgaf_lineItem` WHERE `requestId`=' . $reqId;
		mysqli_query($link, $sql);
	}else{
		$sql = 'INSERT INTO `eresgaf_request`(`organization`, `creator`, `email`, `budgetItem`';
		$vals = '' . '"' . $_POST['organization'] . '" ';
		$vals .= ', "' . $_POST['creator'] . '", "' . $_POST['email'] . '", "' . $_POST['budgetItem'] . '"';

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
		$reqId = 0;
		$sql = 'SELECT `id` FROM `eresgaf_request` WHERE `organization` = "';
		$sql .= $_POST['organization'] . '" AND `creator`=' . '"' . $_POST['creator'] . '" ORDER BY date DESC';
		$results = mysqli_query($link, $sql);
		if($row = mysqli_fetch_assoc($results)){
			$reqId = (int)($row['id']);
		}
		
	}
	$numLines = $_POST['numLines'];
		

	for($i = 1; $i <= $numLines; $i++){
		$sql = 'INSERT INTO `eresgaf_lineItem`(`requestId`, `description`, `venderName`, `cost`, `sgaAllocation`, `clubAccount`';
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
	require "sendEmail.php";
	$notify = $_POST['notifyPeople'];
	$emails = [];
	$advisor = ''; 
	$sql = 'SELECT * FROM `eresgaf_privilegedUser`';
	$results = mysqli_query($link, $sql);
	while ($row = mysqli_fetch_assoc($results)){
		$emails[] = $row['email'];
		if($row['isAdviser'] == 1)
			$advisor = $row['email'];
	}
	if($notify == 'all'){
		newRequest($reqId, $emails);
	}else{
		newRequest($reqId, [$notify, $advisor]);
	}
?>

</div>
<script type="text/javascript">
	window.onload = function(){
		document.getElementById("navNew").className += "navcurrent";
	}
</script>

<?php include "includes/footer.php";?>