<?php include "includes/header.php";

	if(isset($_POST['resId'])){
		$sql = "SELECT * FROM `eresgaf_request` WHERE `id` = " . $_POST['resId'];
		$results = mysqli_query($link, $sql);
		if($row = mysqli_fetch_assoc($results)){
			$resgaf = $row;
			$sql = "SELECT * FROM `eresgaf_lineItem` WHERE `requestId` = " . $resgaf['id'];
			$results = mysqli_query($link, $sql);
			$lineItems = array();
			while ($row = mysqli_fetch_assoc($results)){
				$lineItems[] = $row;
			}
		}
	}
?>

<div class="main-content"> 
	<div class="subtitle">
		New RESGAF
	</div>
	<form id = "newreq" method="post" action="submitRequest.php">
		<fieldset id = "page1">
			<div class="subtitle">
				Page 1/2 Basic Information
			</div>
			<div class="error" id="page1Error"> 
				Enter all fields with a * next to them.
			</div>
			<div class="inputs">
				Name <input type="text" name="creator" size="65" maxlength="100" value="<?php echo $_SERVER['givenName'] . " " . $_SERVER['sn'];?>" required><br> <br>
				Organization <input type="text" name="organization" size="60" value="<?php echo $resgaf['organization'];?>" required> <br> <br>
				Email <input type="text" name="email" size="40" maxlength="50" value="<?php echo $_SERVER['mail'];?>" required>
				Phone <input type="text" name="phone" size="15" maxlength="15" value="<?php echo $resgaf['phone'];?>" required> <br> <br>
				Type of Expenditure 
				<select name="type" id = "type" class="oneline">
					<option value="event" <?php if($resgaf['eventType'] == 1)echo 'selected';?>>Event</option>
					<option value="other" <?php if($resgaf['eventType'] == 0)echo 'selected';?>>Other</option>
				</select> <br> <br>
				<span id = "eventType" >
					
					Event Name <span class="required">*</span><input type="text" name="eventName" id="eventName" maxlength="100" value="<?php echo $resgaf['eventName'];?>">
					Date <span class="required">*</span> <input type="date" name="eventDate" id="eventDate" value="<?php echo date('Y-m-d', strtotime($resgaf['eventDateTime']));?>">
					Time <span class="required">*</span> <input type="time" name="eventTime" id="eventTime" value="<?php echo date('H:i:s', strtotime($resgaf['eventDateTime']));?>"><br> <br>
				</span>
				<span id = "otherType" >
					Description of Expenditure <span class="required">*</span><textarea name="description" rows="3" id = "descrption" ><?php echo $resgaf['expenditureDescription'];?></textarea> <br><br>
				</span>
				SGA Budget Line Item:
				<select name="budgetItem" class="oneline" required>
					<option disabled selected ></option>
					<?php
						require "../../cgi-bin/mysqlcred.php";
						$sql = 'SELECT * FROM `eresgaf_budgetitem`';
						$results = mysqli_query($link, $sql);
						while ($row = mysqli_fetch_assoc($results)){
							echo '<option value="' . $row['name']. '"';
							if($resgaf['budgetItem'] == $row['name'])
								echo 'selected';
							echo '>' . $row['name'] . '</option>';
					
						}
					?>
				</select>
				<br> <br>
				Choose who to 
				<select name="notifyPeople" class="oneline" required>
					<?php
						require "../../cgi-bin/mysqlcred.php";
						$sql = 'SELECT * FROM `eresgaf_privilegedUser`';
						$results = mysqli_query($link, $sql);
						$order = ['Notify all', '', ''];
						$val = ['all', '', ''];
						while ($row = mysqli_fetch_assoc($results)){
							if($row['isPresident'] == 1){
								$order[1] = 'SGA President - ' . $row['name'];
								$val[1] = $row['email'];
							}else if($row['isTreasurer'] == 1){
								$order[2] = 'SGA Tresurer - ' . $row['name'];
								$val[2] = $row['email'];
							}
						}

						for($i = 0; $i < count($order); $i++){
							if(strlen($order[$i]) > 0)
								echo '<option value="' . $val[$i] . '">' . $order[$i] . '</option>';
						}
					?>
				</select>
 				<button id="next" type="button" class="navButton greenButton" >Next</button>
			</div>
		</fieldset>
		<fieldset id = "page2">
			<div class="subtitle">
				Page 2/2 Line Items
			</div>
			<table id="lineItems">
				<tr id="line0">
					<th></th>
					<th>Description</th>
					<th>Cost</th>
					<th>SGA Allocation</th>
					<th>Carry Over Club Account</th>
				</tr>
				<?php
					$divs = '';
					$sgatot = 0;
					$clubtot = 0;
					if(isset($lineItems)){
						for($i = 0; $i < count($lineItems); $i++){
							$row = '<tr id="line' . ($i + 1) . '">';
							$row .= '<td><button type="button" id="close' . ($i + 1) . '" onclick="remove(' . ($i + 1) . ');" class="remove" id="remove' . ($i + 1) . '">X</td>';
							$row .= '<td>' . $lineItems[$i]['description'] . '</td>';
							$row .= '<td>$' . $lineItems[$i]['cost'] . '</td>';
							$row .= '<td>$' . $lineItems[$i]['sgaAllocation'] . '</td>';
							$sgatot += $lineItems[$i]['sgaAllocation'];
							$row .= '<td>$' . $lineItems[$i]['clubAccount'] . '</td>';
							$clubtot += $lineItems[$i]['clubAccount'];
							$row .= '<td class="edits"><button class="navButton editButton" type="button" id="edit' . ($i + 1) . '" onclick="edit(' . ($i + 1) . ');">Edit</button> </td></tr>';
							echo $row;

							$newdiv = '<div class = "lineItemDiv" id="newLine' . ($i + 1) . '">';
							$newdiv .= '<button type="button" class="close"> X</button>';
							$newdiv .= '<div class="subtitle"> Add New LineItem</div>';
							$newdiv .= '<div class="error" id="error' . ($i + 1) . '"> The description, two costs and vender are required.</div>';
							$newdiv .= '<div class="inputs">';
							$newdiv .= 'Description <input type="text" value="' . $lineItems[$i]['description'] . '" name="descriptionLine' . ($i + 1) . '" id="descriptionLine' . ($i + 1) . '" size="70" required> <br> <br>';
							$newdiv .= '<div id="costs"> <span style="margin:0px;" class="smallWidth">SGA Allocation</span> <input type="number" value="' . $lineItems[$i]['sgaAllocation'] . '"" name="SGAall' . ($i + 1) . '" id="SGAall' . ($i + 1) . '" class="num" required>';
							$newdiv .= '<span class="smallWidth">Carry Over Club Account</span> <input type="number" value="' . $lineItems[$i]['clubAccount'] . '" name= "clubacc' . ($i + 1) . '" id= "clubacc' . ($i + 1) . '" class="num" required></div> <br>';
							$newdiv .= 'vender/payee <input type="text" value="' . $lineItems[$i]['venderName'] . '" name="vender' . ($i + 1) . '" id="vender' . ($i + 1) . '" size="25" maxlength="100" required>';
							$newdiv .= '<span class="smallWidth">Phone</span> <input type="text" value="' . $lineItems[$i]['phone'] . '" name="phone' . ($i + 1) . '" id="phone' . ($i + 1) . '" size="20" maxlength="15"> <br> <br>';
							$newdiv .= 'Address <input type="text"  value="' . $lineItems[$i]['address'] . ' " name="address' . ($i + 1) . '" id="address' . ($i + 1) . '" size="70"> <br> <br>';
							$newdiv .= 'FIN/SSN <input type="text"  value="' . $lineItems[$i]['finssn'] . '" name="finssn' . ($i + 1) . '" id="finssn' . ($i + 1) . '" size="20" maxlength="20">';
							$newdiv .= '<div class="smallWidth">Contact Person</div> <input type="text"  value="' . $lineItems[$i]['contactPerson'] . '" name="contact' . ($i + 1) . '" id="contact' . ($i + 1) . '" maxlength=100 size="30"> <br> <br>';
							$newdiv .= '<button type="button" id="addrow' . ($i + 1) . '" onclick="addrow(' . ($i + 1) . ');" class="submitLone navButton greenButton">Submit</button>';
							$newdiv .= '</div></div>';
							$divs = $newdiv . $divs;
						}
					}
				?>
				<tr id="lastRow">
					<td><button id="add" type="button">+</button></td>
					<td class="rightText">Total</td>
					<td id="cost">$<?php echo ($sgatot + $clubtot);?></td>
					<td id="sgaAllc">$<?php echo $sgatot;?></td>
					<td id="club">$<?php echo $clubtot;?></td>
				</tr>
			</table>
			<?php echo $divs;?>
			<input type="submit" id="submit" class="navButton greenButton">
			<button type="button" id="prev" class="navButton">Back</button>

			<div id="screen" hidden> </div> 
			<input type="number" name="numLines" id="numLines" value="<?php echo count($lineItems);?>" hidden>
			<input type="number" name="resId" value="<?php echo $resgaf['id'];?>" hidden>
		</fieldset>
	</form>
</div>

<script type="text/javascript">
	var costTotal = <?php echo $sgatot + $clubtot;?>;
	var sgaAlTotal = <?php echo $sgatot;?>;
	var clubAcTotal = <?php echo $clubtot;?>;
	var lineNum = <?php echo(count($lineItems));?>;
	window.onload = function(){
		document.getElementById("navNew").className += "navcurrent";
		$('.lineItemDiv').hide();

		$('.close').click(function(){
			$('#newLine' + lineNum).remove();
			$('#screen').hide();
		})

		$(':required').before('<span class="required">*</span>');
		var test = '<?php echo $resgaf['eventType'] == 1;?>';
		if( test.length > 0){
			$('#otherType').hide();
		}
		else{
			$('#eventType').hide();
		}
		$('.error').hide();
		$('#type').change(function(){
			var val = $(this).val();
			if(val == 'event'){
				$('#otherType').hide();
				$('#eventType').show();
				document.getElementById('eventName').required = true;
				document.getElementById('eventDate').required = true;
				document.getElementById('eventTime').required = true;
				document.getElementById('descrption').required = false;
			}else if(val == 'other'){
				$('#eventType').hide();
				$('#otherType').show();
				document.getElementById('eventName').required = false;
				document.getElementById('eventDate').required = false;
				document.getElementById('eventTime').required = false;
				document.getElementById('descrption').required = true;
			}
		})
		
		$('#next').click(function(){
			var reqs = $('#page1 :required');
			$('#page1Error').show();
			var next = true;
			$.each(reqs, function(){
				if (this.value.length == 0){
					next = false;
					return;
				}
			})
			
			if($('#type').val() == 'event'){
				if($('#eventName').val().length == 0 || $('#eventDate').val().length == 0 || $('#eventTime').val().length == 0){
					next = false;
				}
			}else if($('#descrption').val().length == 0){
				next = false;
			}

			if(next){
				$('#page1Error').hide();
				$('#page1').hide();
				$('#page2').show();
			}
		})

		$('#add').click(function(){
			$('#screen').show();
			lineNum++;
			var newItem = '<div class = "lineItemDiv" id="newLine' + lineNum + '">';
			newItem += '<button type="button" class="close"> X</button>';
			newItem += '<div class="subtitle"> Add New LineItem</div>';
			newItem += '<div class="error" id="error' + lineNum + '"> The description, two costs and vender are required.</div>';
			newItem += '<div class="inputs">';
			newItem += 'Description <input type="text" name="descriptionLine' + lineNum + '" id="descriptionLine' + lineNum + '" size="70" required> <br> <br>';
			newItem += '<div id="costs"> <span style="margin:0px;" class="smallWidth">SGA Allocation</span> <input type="number" name="SGAall' + lineNum + '" id="SGAall' + lineNum + '" class="num" required>';
			newItem += '<span class="smallWidth">Carry Over Club Account</span> <input type="number" name= "clubacc' + lineNum + '" id= "clubacc' + lineNum + '" class="num" required></div> <br>';
			newItem += 'vender/payee <input type="text" name="vender' + lineNum + '" id="vender' + lineNum + '" size="25" maxlength="100" required>';
			newItem += '<span class="smallWidth">Phone</span> <input type="text" name="phone' + lineNum + '" id="phone' + lineNum + '" size="20" maxlength="15"> <br> <br>';
			newItem += 'Address <input type="text" name="address' + lineNum + '" id="address' + lineNum + '" size="70"> <br> <br>';
			newItem += 'FIN/SSN <input type="text" name="finssn' + lineNum + '" id="finssn' + lineNum + '" size="20" maxlength="20">';
			newItem += '<div class="smallWidth">Contact Person</div> <input type="text" name="contact' + lineNum + '" id="contact' + lineNum + '" maxlength=100 size="30"> <br> <br>';
			newItem += '<button type="button" id="addrow' + lineNum + '" onclick="addrow(' + lineNum + ');" class="submitLone navButton greenButton">Submit</button>';
			newItem += '</div></div>';
			$('#lineItems').after(newItem);

			$('#lineNum').val(lineNum);

			$('#newLine' + lineNum + ' :required').before('<span class="required">*</span>');
			$('.close').click(function(){
				$('#newLine' + lineNum).remove();
				$('#screen').hide();
				lineNum--;
			})

			$('#error' + lineNum).hide();
		})



		$('#prev').click(function(){
			$('#page2').hide();
			$('#page1').show();
		})

		
	}
	function addrow(num){
		var maxnum = lineNum;
		lineNum = num;
		var description = $('#descriptionLine' + lineNum).val();
		var sgaAl = $('#SGAall' + lineNum).val();
		var clubAc = $('#clubacc' + lineNum).val();
		var vender = $('#vender' + lineNum).val();
		var cost = parseInt(sgaAl) + parseInt(clubAc);
		
		if(description.length == 0 || sgaAl.length == 0 || clubAc.length == 0 || vender.length == 0){
			$('#error' + lineNum).show();
			return;
		}

		costTotal += parseInt(cost);
		sgaAlTotal += parseInt(sgaAl);
		clubAcTotal += parseInt(clubAc);

		$('#cost').text('$' + costTotal);
		$('#sgaAllc').text('$' + sgaAlTotal);
		$('#club').text('$' + clubAcTotal);

		$('#line' + lineNum).remove();
		var newRow = '<tr id = "line' + lineNum + '"><td><button type="button" id="close' + lineNum + '" onclick="remove(' + lineNum + ');" class="remove" id="remove' + lineNum + '">X</td>';
		newRow += '<td>' + description + '</td>';
		newRow += '<td>$' + cost + '</td>';
		newRow += '<td>$' + sgaAl + '</td>';
		newRow += '<td>$' + clubAc + '</td>';
		newRow += '<td class="edits"><button class="navButton editButton" type="button" id="edit' + lineNum + '" onclick="edit(' + lineNum + ');">Edit</button> </td></tr>';

		$('#line' + (lineNum - 1)).after(newRow);

		$('#screen').hide();
		$('#newLine' + lineNum).hide();
		lineNum = maxnum;
		$('#numLines'	).val(lineNum);
	}

	function remove(num){
		$('#lineItems tr:nth-child(' + (num + 1) + ')').remove();

		var sgaAl = $('#SGAall' + num).val();
		var clubAc = $('#clubacc' + num).val();
		var cost = parseInt(sgaAl) + parseInt(clubAc);
		costTotal -= parseInt(cost);
		sgaAlTotal -= parseInt(sgaAl);
		clubAcTotal -= parseInt(clubAc);
		$('#cost').text('$' + costTotal);
		$('#sgaAllc').text('$' + sgaAlTotal);
		$('#club').text('$' + clubAcTotal);
		$('#newLine' + num).remove();

		for(var i = num + 1; i <= lineNum; i++){
			$('#line' + i).attr("id", "line" + (i - 1));
			$('#edit' + i).attr("onclick", 'edit(' + (i - 1) + ');').attr("edit" + (i - 1));
			$('#newLine' + i).attr("id", "newLine" + (i - 1));
			$('#error' + i).attr("id", "error" + (i - 1));
			$('#descriptionLine' + i).attr("id", 'descriptionLine' + (i - 1)).attr("name", 'descriptionLine' + (i - 1));
			$('#SGAall' + i).attr("id", 'SGAall' + (i - 1)).attr("name", 'SGAall' + (i - 1));
			$('#clubacc' + i).attr("id",'clubacc' + (i - 1)).attr("name", 'clubacc' + (i - 1));
			$('#vender' + i).attr("id", 'vender' + (i - 1)).attr("name", 'vender' + (i - 1));
			$('#phone' + i).attr("id", 'phone' + (i - 1)).attr("name", 'phone' + (i - 1));
			$('#address' + i).attr("id", 'address' + (i - 1)).attr("name" , 'address' + (i - 1));
			$('#finssn' + i).attr("id", 'finssn' + (i - 1)).attr("name", 'finssn' + (i - 1));
			$('#contact' + i).attr("id", 'contact' + (i - 1)).attr("name", 'contact' + (i - 1));
			$('#addrow' + i).attr("id", 'addrow' + (i - 1)).attr("onclick", 'addrow(' + (i - 1) + ');')
			$('#edit' + i).attr("onclick", 'edit(' + (i - 1) + ');').attr('id', 'edit' + (i - 1));

		}
		lineNum--;
	}

	function edit(num){
		var sgaAl = $('#SGAall' + num).val();
		var clubAc = $('#clubacc' + num).val();
		var cost = parseInt(sgaAl) + parseInt(clubAc);
		costTotal -= parseInt(cost);
		sgaAlTotal -= parseInt(sgaAl);
		clubAcTotal -= parseInt(clubAc);

		$('#screen').show();
		$('#newLine' + num).show();
	}
	
</script>

<?php include "includes/footer.php";?>
