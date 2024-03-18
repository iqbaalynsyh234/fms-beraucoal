<?php
	if (! isset($_POST['sender']))
	{
		$_POST['sender'] = "cron@lacak-mobil.com";
	}

	$headers = "";
	
	if (isset($_POST['format']) && ($_POST['format'] == 'html'))
	{
		$headers  .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";		
	}
	
	if (isset($_POST['bcc']))
	{
		$headers .= 'Bcc: '.$_POST['bcc'] . "\r\n";
	}

	$headers .=  'From: '.$_POST['sender'] . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
    	echo $_POST['sender'];
	mail($_POST['dest'], $_POST['subject'], $_POST['message'], $headers);
