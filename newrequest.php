<?php include "includes/header.php";?>

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
				Name <input type="text" name="name" size="65" maxlength="100" required><br> <br>
				Orginization <input type="text" name="organization" size="60" required> <br> <br>
				Email <input type="text" name="email" size="40" maxlength="50" required>
				Phone <input type="text" name="phone" size="15" maxlength="15" required> <br> <br>
				Type of Expenditure 
				<select name="type" id = "type" class="oneline" >
					<option value="event">Event</option>
					<option value="other">Other</option>
				</select> <br> <br>
				<span id = "eventType" >
					
					Event Name <input type="text" name="eventName" id="eventName" maxlength="100" required>
					Date <input type="date" name="eventDate" id="eventDate" required>
					Time <input type="time" name="eventTime" id="eventTime" required> <br> <br>
				</span>
				<span id = "otherType" >
					Description of Expenditure <textarea name="description" rows="3" id = "descrption"></textarea>
				</span>
				SGA Budget Line Item:
				<select name="budgetItem" class="oneline" required>
					<?php
						require "../../cgi-bin/mysqlcred.php";
						$sql = 'SELECT * FROM `eresgaf_budgetitem`';
						$results = mysqli_query($link, $sql);
						while ($row = mysqli_fetch_assoc($results)){
							echo '<option value="' . $row['name']. '">' . $row['name'] . '</option>';
					
						}
					?>
				</select>
				<br> <br>
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
				<tr id="lastRow">
					<td><button id="add" type="button">+</button></td>
					<td class="rightText">Total</td>
					<td id="cost"></td>
					<td id="sgaAllc"></td>
					<td id="club"></td>
				</tr>
			</table>
			<input type="submit" id="submit" class="navButton greenButton">
			<button type="button" id="prev" class="navButton">Back</button>

			<div id="screen" hidden> </div> 
			<input type="number" name="numLines" id="numLines" hidden>
		</fieldset>
	</form>
</div>

<script type="text/javascript">
	var costTotal = 0;
	var sgaAlTotal = 0;
	var clubAcTotal = 0;
	var lineNum = 0;
	window.onload = function(){
		document.getElementById("navNew").className += "navcurrent";
		$(':required').before('<span class="required">*</span>');
		$('#otherType').hide();
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
			var next = true;
			$.each(reqs, function(){
				if (this.value.length == 0){
					$('#page1Error').show();
					next = false;
					return;
				}
			})
			// if(next){
				$('#page1Error').hide();
				$('#page1').hide();
				$('#page2').show();
			// }
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