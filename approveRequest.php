<?php 
	include "includes/header.php";
	require "sendEmail.php";

?>
<div class="main-content">
	<?php
		if(isset($_POST['resId'])){
			$id = $_POST['resId'];
			echo '<div class="subtitle info">RESGAF #' . $id . ' Approved</div>';
			$email = $_SERVER['mail'];
			$sql = "SELECT * FROM `eresgaf_privilegedUser` WHERE `email` = '" . $email. "'";
			$results = mysqli_query($link, $sql);
			$maxReq = 0;
			$approvePerson = "";
			if ($row = mysqli_fetch_assoc($results)){
				$maxReq = $row['maxAmount'];
				if($row['isPresident'] == 1){
					$sql = "UPDATE `eresgaf_request` SET `presidentApproval`= '" . date('Y-m-d') . "' WHERE `id`=" . $id;
					$approvePerson = "SGA President";
					mysqli_query($link, $sql);
				}else if($row['isTreasurer'] == 1){
					$sql ="UPDATE `eresgaf_request` SET `treasurerApproval`= '" . date('Y-m-d') . "' WHERE `id`=" . $id;
					$approvePerson = "SGA Tresurer";
					mysqli_query($link, $sql);
				}else if($row['isAdviser'] == 1){
					$sql ="UPDATE `eresgaf_request` SET `advisorApproval`= '" . date('Y-m-d') . "' WHERE `id`=" . $id;
					$approvePerson = "SGA Advisor";
					mysqli_query($link, $sql);
				}


				$sql = "SELECT sum(`cost`) as total FROM `eresgaf_lineItem` WHERE `requestId` = " . $id;
				$query = "SELECT * FROM `eresgaf_request` WHERE `id`=" . $id;
				$res = mysqli_query($link, $query);
				$results = mysqli_query($link, $sql);
				$resgaf = mysqli_fetch_assoc($res);

				if(strlen($approvePerson) > 0){
					approvedBy($resgaf['organization'], $resgaf['email'], $approvePerson);
				}
				$approve = false;
				$sql = "UPDATE `eresgaf_request` SET `approved`='" . date("Y-m-d") . "' WHERE `id`=" . $id;
				if ($row = mysqli_fetch_assoc($results)){
					if($resgaf['advisorApproval']){
						if($row['total'] < $maxReq){
							if(($resgaf['presidentApproval']||$resgaf['treasurerApproval']) && !$resgaf['approved']){
								$approve = true;
							}
						}else{
							if($resgaf['presidentApproval'] && $resgaf['treasurerApproval'] && !$resgaf['approved']){
								$approve = true;
							}
						}
					}
				}

				if($approve){
					mysqli_query($link, $sql);
					approved($resgaf['organization'], $resgaf['email']);
				}
			}
		}
	?>
</div>
<?php include "includes/footer.php";?>
