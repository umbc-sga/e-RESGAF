<?php include "includes/header.php";?>

<div class="main-content"> 
	<div class="subtitle">
		New RESGAF
	</div>
	<form id = "newreq">
		<fieldset id = "page1">
			<div class="subtitle">
				Page 1/2 Basic Information
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
				<tr>
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
			<div id="screen" hidden> </div> 
			<input type="number" name="numLines" id="numLines" hidden>
		</fieldset>
	</form>
</div>

<script type="text/javascript">
	var costTotal = 0;
	var sgaAlTotal = 0;
	var clubAcTotal = 0;
	window.onload = function(){
		var lineNum = 0;
		document.getElementById("navNew").className += "navcurrent";
		$('#otherType').hide();
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
			$('#page1').hide();
			$('#page2').show();
		})

		$('#add').click(function(){
			$('#screen').show();
			lineNum++;
			var newItem = '<div class = "lineItemDiv" id="newLine' + lineNum + '">';
			newItem += '<button type="button" class= "close"> X</button>';
			newItem += '<div class="subtitle"> Add New LineItem</div>';
			newItem += '<div class="inputs">';
			newItem += 'Description <input type="text" name="descriptionLine' + lineNum + '" id="descriptionLine' + lineNum + '" size="70" required> <br> <br>';
			newItem += 'Cost <input type="number" name="cost' + lineNum + '" id="cost' + lineNum + '" class="num" required>';
			newItem += '<span class="smallWidth">SGA Allocation</span> <input type="number" name="SGAall' + lineNum + '" id="SGAall' + lineNum + '" class="num" required>';
			newItem += '<span class="smallWidth">Carry Over Club Account</span> <input type="number" name= "clubacc' + lineNum + '" id= "clubacc' + lineNum + '" class="num" required> <br> <br>';
			newItem += 'vender/payee <input type="text" name="vender' + lineNum + '" size="25" maxlength="100" required>';
			newItem += '<span class="smallWidth">Phone</span> <input type="text" name="phone' + lineNum + '" size="20" maxlength="15"> <br> <br>';
			newItem += 'Address <input type="text" name="address' + lineNum + '" size="70"> <br> <br>';
			newItem += 'FIN/SSN <input type="text" name="finssn' + lineNum + '" size="20" maxlength="20">';
			newItem += '<div class="smallWidth">Contact Person</div> <input type="text" name="contact' + lineNum + '" maxlength=100 size="30"> <br> <br>';
			newItem += '<button type="button" class="submitLone navButton greenButton">Submit</button>'
			newItem += '</div></div>';
			$('#lineItems').after(newItem);

			$('#lineNum').val(lineNum);

			$('.close').click(function(){
				$('#newLine' + lineNum).remove();
				$('#screen').hide();
				lineNum--;
			})

			$('.submitLone').click(addrow);
		})

		var addrow = function(){
			var descrption = $('#descriptionLine' + lineNum).val();
			var cost = $('#cost' + lineNum).val();
			var sgaAl = $('#SGAall' + lineNum).val();
			var clubAc = $('#clubacc' + lineNum).val();
			
			if(cost.length == 0 || sgaAl.length == 0 || clubAc.length == 0){
				return;
			}
			costTotal += parseInt(cost);
			sgaAlTotal += parseInt(sgaAl);
			clubAcTotal += parseInt(clubAc);

			$('#cost').text('$' + costTotal);
			$('#sgaAllc').text('$' + sgaAlTotal);
			$('#club').text('$' + clubAcTotal);

			var newRow = '<tr id = "line' + lineNum + '"><td>button</td>';
			newRow += '<td>' + descrption + '</td>';
			newRow += '<td>$' + cost + '</td>';
			newRow += '<td>$' + sgaAl + '</td>';
			newRow += '<td>$' + clubAc + '</td></tr>';

			$('#lastRow').before(newRow);

			$('#screen').hide();
			$('#newLine' + lineNum).hide();
		}
	}
</script>

<?php include "includes/footer.php";?>