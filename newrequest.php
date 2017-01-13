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
			Orginization <input type="text" name="organization" size="60"> <br> <br>
			Email <input type="text" name="email" size="40" maxlength="50">
			Phone <input type="text" name="phone" size="15" maxlength="15">
		</div>
	</fieldset>
	<fieldset id = "page2">
		<div class="subtitle">
			Page 2/2 Line Items
		</div>
		
	</fieldset>
</form>
</div>

<script type="text/javascript">
	window.onload = function(){
		document.getElementById("navNew").className += "navcurrent";
	}
</script>
