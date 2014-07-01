<?php

chdir(dirname(__FILE__)); //since we call from a cron job, we have to change the current directory we are working in
//NR 04/11/14 moved all db connections to dbconn
if(file_exists("./Variables.php"))
	require("./Variables.php");
else
	require("../Variables.php");
require($root."dbconn.php");

//This is needed since we are calling from a command line, but to keep compatibility with calling from a web enviroment
foreach ($argv as $arg) {
	$e=explode("=",$arg);
	if(count($e)==2)
		$_GET[$e[0]]=$e[1];
	else
		$_GET[$e[0]]=0;
}

//TESTING
//$_GET['emailToSend'] = "5DaysPriorToDeadline";
//The page is called by the cron job to send a message
if($_GET['emailToSend']){
	
	//sendTo denotes who will receive the email. 1 = all, 0 = people who have not yet entered information
	switch($_GET['emailToSend']){
		case "2DaysPriorToOpening":
			$subjectColumn = "twoDaysPriorToOpeningSubject";
			$bodyColumn = "twoDaysPriorToOpeningBody";
			$sendTo = 1;
			break;
		case "systemOpening":
			$subjectColumn = "systemOpeningSubject";
			$bodyColumn = "systemOpeningBody";
			$sendTo = 1;
			break;
		case "5DaysPriorToDeadline":
			$subjectColumn = "fiveDaysPriorToDeadlineSubject";
			$bodyColumn = "fiveDaysPriorToDeadlineBody";
			$sendTo = 0;
			break;
		case "countdownToDeadline":
			$subjectColumn = "countdownToDeadlineSubject";
			$bodyColumn = "countdownToDeadlineBody";
			$sendTo = 0;
			break;
	}

	$sql = "SELECT ".$subjectColumn.", ".$bodyColumn.", fromAddress FROM reminderEmail";
		
	$result = $db->get_row($sql);

	$subject = $result->$subjectColumn;

	$body = $result->$bodyColumn;
	//Various formating requirements
	$body = chunk_split($body, 70, "\r\n");
	$body = str_replace("\n.", "\n..", $body);
	
	$from = $result->fromAddress;
	
	/*****Here we get which centers we are emailing*****/
	switch (date("m")){
		case 10:
		case 11:
		case 12:
			$fiscalYear = date("Y");
			$currentQuarter = 4;
			break;
		case 1:
		case 2:
		case 3:
			$fiscalYear = date("Y");
			$currentQuarter = 1;
			break;
		case 4:
		case 5:
		case 6:
			$fiscalYear = date("Y");
			$currentQuarter = 2;
			break;
		case 7:
		case 8:
		case 9:
			$fiscalYear = date("Y");
			$currentQuarter = 3;
			break;
	}
	
	switch($sendTo){
		case 0:
			$sql = "SELECT email from directors LEFT JOIN actualExpenditures ON directors.center = actualExpenditures.center and actualExpenditures.fiscalyear = ".$fiscalYear." and actualExpenditures.quarter = ".$currentQuarter." and actualExpenditures.completed = 'INC' WHERE directors.center not in (0,99)  and actualExpenditures.completed <> 'COM' UNION SELECT email FROM directors WHERE center not in (SELECT center FROM actualExpenditures WHERE fiscalyear = ".$fiscalYear." and quarter = ".$currentQuarter.") and center not in (0,99)";
			break;
		case 1:
			$sql = 'SELECT email from directors WHERE center NOT IN ("99")';
			break;
	}
	
	$email_to_result = $db->get_results($sql);
	
//	echo "<pre>";
//	print_r($email_to_result);
//	echo "</pre>";
	$email_to = '';
	foreach ($email_to_result as $center){
		$email_to .= rtrim($center->email, ",").", ";
	}
	//Remove trailing "," and " "
	$email_to = substr($email_to, 0, -2);
	
	//echo "<pre>";
	//print_r($email_to);
	//echo "</pre>";
	//$email_to = 'revelsn@gmail.com';
	
	$headers = 'From: '.$from."\r\n".

			'Reply-To: '.$from."\r\n" .

			'X-Mailer: PHP/' . phpversion();
	
//	echo "Headers: ".$headers."<br>Subject: ".$subject."<br>Body: ".$body;

	mail($email_to, $subject, $body, $headers);

}
else
	die("Didn't get an email to send");
?>
