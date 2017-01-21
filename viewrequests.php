<?php include "includes/header.php";?>

<div class="main-content"> 
	<div class="subtitle">
		View RESGAFs
	</div>
	<br>
	<table class="middle">
		<tr>
			<th>Date</th>
			<th>Organization</th>
			<th>Description</th>
			<th>Total Amount</th>
			<th>Status</th>
			<th></th>
		</tr>
		<?php
			require "../../cgi-bin/mysqlcred.php";
			$sql = "SELECT * FROM `eresgaf_request` ORDER BY `date` DESC";
			$results = mysqli_query($link, $sql);
			while ($row = mysqli_fetch_assoc($results)){
				echo '<tr>';
				$date = strtotime($row['date']);
				$strDate = date('F d, Y ', $date);
				echo '<td>' . $strDate . '</td>';
				echo '<td>' . $row['organization'] . '</td>';
				echo '<td>';
				if ($row['eventType']){
					echo $row['eventName'];
				}else{
					echo $row['expenditureDescription'];
				}
				echo '</td> <td>';
				$sql = "SELECT sum(`cost`) AS total FROM `eresgaf_lineItem` WHERE `requestId` = " . $row['id'];
				$info = mysqli_query($link, $sql);
				if($tot = mysqli_fetch_assoc($info)){
					echo '$' . $tot['total'];
				}
				echo '</td><td>';
				if($row['approved']){
					echo 'Approved';
				}else{
					echo 'Pending';
				}
				echo '</td><td class="edits"><a href="request.php?reqId=' . $row['id'] . '"><button class="navButton editButton" type="button">View</button>';
				echo '</tr>';
			}
		?>
	</table>
</div>

<script type="text/javascript">
	window.onload = function(){
		document.getElementById("navView").className += "navcurrent";
	}
</script>

<?php include "includes/footer.php";?>
