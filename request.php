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
		<span class ="sectionTitle">Requester</span><br>
		<div class="inputs">
			Name: <?php echo $resgaf['creator'];?> <br><br>
			Email: <?php echo $resgaf['email'];?><br><br>
			Phone: <?php echo $resgaf['phone'];?><br><br>
			Organization: <?php echo $resgaf['organization'];?><br>
		</div>
	</div>
	<div class="info ">
		<span class ="sectionTitle">Purpose</span><br>
		<div class="inputs">
			<?php
				if($resgaf['expenditureDescription']){
					echo "Event Description: " . $resgaf['expenditureDescription'];
				}else{
					echo "Event Name: " . $resgaf['eventName'] . '<br><br>';
					$date = strtotime($resgaf['eventDateTime']);
					$strDate = date('F d, Y ', $date);
					echo 'Date: ' . $strDate . '<br><br>';
					$strTym = date('h:i a', $date);
					echo 'Time: ' . $strTym . '<br>';
				}
			?>
		</div>
	</div>
	<br><br>
	<div class ="subtitle"> Line Items - Clock each for details</div>
	<table class="middle">
		<tr>
			<th>Description</th>
			<th>Cost</th>
			<th>SGA Allocation</th>
			<th>Carry Over Club Account</th>
		</tr>
	<?php
		$sql = "SELECT * FROM `eresgaf_lineItem` WHERE `requestId`= " . $resgaf['id'];
		$results = mysqli_query($link, $sql);
		$i = 0;
		$sga = 0;
		$club = 0;
		while($row = mysqli_fetch_assoc($results)){
			$i++;
			$sga += $row['sgaAllocation'];
			$club += $row['clubAccount'];
			echo '<tr class = "description" id="line' . $i . '">';
			echo '<td>' . $row['description'] . '</td>';
			echo '<td> $' . $row['cost'] . '</td>';
			echo '<td> $' . $row['sgaAllocation'] . '</td>';
			echo '<td> $' . $row['clubAccount'] . '</td>';
			echo '</tr>';
			echo '<tr class="lineInfo" id = "info' . $i . '" class="lines">';
			echo '<td colspan="4" >';
			echo ' <div class="info wide" >';
			echo 'Vender/Payee: ' . $row['venderName'];
			if($row['contactPerson'])
				echo '<span class="rightTab">Contact Person: ' . $row['contactPerson'] . '</span> <br><br>';
			if($row['address'])
				echo 'Address: ' . $row['address'] . '<br><br>';
			if($row['phone'])
				echo 'Phone: ' . $row['phone'];
			if($row['finssn'])
				echo '<span class="rightTab">FIN/SSN' . $row['finssn'] . '</span><br>';
			echo '</div></td> </tr>';
		}
		echo '<tr><td class="rightText">Total</td>';
		echo "<td>$" . ($sga + $club) . '</td>';
		echo '<td>$' . $sga . '</td>';
		echo '<td>$' . $club . '</td>';
		echo '</tr>';
	?>
	</table>
	<div class="navs">
		<button type="button" id="prev" class="navButton greenButton">Approve</button>
		<button type="button" id="prev" class="navButton purpleButton">Edit </button>
	</div>
	<div class ="subtitle">Comments</div>

	<?php
		$sql = "SELECT * FROM `eresgaf_comment` WHERE `requestId` = " . $resgaf['id'];
		$results = mysqli_query($link, $sql);
		while($row = mysqli_fetch_assoc($results)){
			$name = $row['creatorName'];
			$comment = $row['content'];
			$commentBox = '<div>';
			if($name == $_SERVER['givenName'] . " " . $_SERVER['sn']){
				$commentBox .= '<div class="info">' . $comment . '</div>';
				$commentBox .= '<div class="editer">' . $name . '</div>';
			}else{
				$commentBox .= '<div class="editer">' . $name . '</div>';
				$commentBox .= '<div class="info">' . $comment . '</div>';
			}
			$commentBox .= '</div>';
			echo $commentBox;
		}
	?>

	<textarea id="newComment" rows="2" > </textarea> 
	<button type="button" class="navButton greenButton" id = "postComment">Post</button>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.description').click(function(){
			// $('.lineInfo').hide();
			var id = this.id.substring(4);
			$('#info' + id).toggle();
		})

		$('#postComment').click(function(){
			var name = "<?php echo $_SERVER['givenName'] . " " . $_SERVER['sn'];?>";
			var email = "<?php echo $_SERVER['mail'];?>";
			var resId = <?php echo $resgaf['id'];?>;
			var comment = $('#newComment').val();
			$.post('createComment.php',{
				'name': name,
				'requestId': resId,
				'text':comment
			}, function(a){alert(a);});

			var commentBox = '<div>';
			if(email == "<?php echo $resgaf['email'];?>"){
				commentBox += '<div class="info">' + comment + '</div>';
				commentBox += '<div class="editer">' + name + '</div>';
			}else{
				commentBox += '<div class="editer">' + name + '</div>';
				commentBox += '<div class="info">' + comment + '</div>';
			}
			commentBox += '</div>';

			$('#newComment').before(commentBox);
		})
	})
</script>
<?php include "includes/footer.php";?>
