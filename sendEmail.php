<?php
	function send($message, $subject, $email){
		$header = "MIME-Version: 1.0" . "\r\n";
		$header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$header .= "From: sga@umbc.edu" . "\r\n" . "CC: sga@umbc.edu";
		$message = wordwrap($message, 70);
		mail($email, $subject, $message, $header);	
	}

	function newComment($commenter, $reqname, $reqcreate, $id){
		$message = $commenter . " has created a comment on your RESGAF for " . $reqname . '.';
		$message .= '  View the RESGAF at sga-dev/umbc.edu/e-resgaf/request.php?id=' . $id;
		send($message, "New commont on UMBC SGA RESGAF", $reqcreate);
	}

	function approved($reqname, $reqcreate){
		$message = "Your UMBC SGA RESGAF for " . $reqname . " has been approved.";
		send($message, 'SGA RESGAF Approved', $reqcreate);
	}

	function approvedby($reqname, $reqcreate, $approver){
		$message = "Your UMBC SGA RESGAF for " . $reqname . " was approved by " . $approver . '.';
		send($message, 'SGA RESGAF Partially Approved', $reqcreate);
	}

	function newRequest($id, $emails){
		$to = '';
		for($i = 0; $i < count($emails); $i++){
			$to .= $emails[$i] . ', ';
		}
		$to = $to.trim(', ');
		$message = 'A new RESGAF has been created.  It can be reviewed at sga-dev.umbc.edu/e-resgaf/request.php?id=' . $id . '.';
		send($message, 'New RESGAF Summited', $to);
	}
?>