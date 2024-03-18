<?php
	
	$uri = $_SERVER["REQUEST_URI"];
	$uris = explode("/", $uri);
	
	if (count($uris) >= 3)
	{
		if (($uris[1] == "app") && ($uris[2] == "mobile"))
		{
			header("location: http://m.".$_SERVER['SERVER_NAME']);
			exit;
		}
	}
	
	header("location: http://".$_SERVER['SERVER_NAME']);