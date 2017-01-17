<?php include "includes/header.php";
	$id = $_GET['id'];
	$sql = "SELECT * FROM `eresgaf_request` WHERE `id` = $id";
	$results = mysqli_query($link, $sql);
	$resgaf = mysqli_fetch_assoc($results);

?>

<div class="main-content">
	<div class="subtitle left">
		RESGAF Status:
		<?php
			$status = "";
			if($resgaf['approved']){
				$date = strtotime($resgaf['approved']);
				$strDate = date('F d, Y ', $date);
				$status = "Approved on " . $strDate;
			}else{
				$people = "";
				if($resgaf['advisorApproval']){
					$people .= 'SGA Advisor';
				}
				if($resgaf['presidentApproval']){
					if(strlen($people) > 0){
						$people .= ', ';
					}
					$people .= "President"; 
				}
				if($resgaf['treasurerApproval']){
					if(strlen($people) > 0){
						$people .= ', ';
					}
					$people .= "Treasurer"; 
				}
				if(strlen($people) == 0){
					$status = "Pennding Approval";
				}else{
					$status = "Approved by " . $people;
				}
			}
			echo $status;
		?>

	</div>
	<br>
		<div class="info">
			Requester
		</div>
		<div class="info ">
			 Purpose<br>
			<?php
				if($resgaf['expenditureDescription']){
					echo "Event Description: " . $resgaf['expenditureDescription'];
				}else{
					echo "Event Name: " . $resgaf['eventName'] . '<br>';
					$date = strtotime($resgaf['eventDateTime']);
					$strDate = date('F d, Y ', $date);
					echo 'Date: ' . $strDate . '<br>';
					$strTym = date('h:i a', $date);
					echo 'Time: ' . $strTym . '<br>';
				}
			?>
		</div>
</div>

<?php include "includes/footer.php";?>
