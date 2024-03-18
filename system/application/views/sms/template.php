<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
	<head>
		<title>GPS Tracker SMS<?php if (isset($this->sess) && isset($this->sess->user_name)) { echo " :: ".$this->sess->user_name; } ?></title>
		<meta name="description" content="<?php echo htmlspecialchars($configs['metakeywords'], ENT_QUOTES);?>" />
		<meta name="keywords" content="<?php echo htmlspecialchars($configs['metakeywords'], ENT_QUOTES);?>" />
		<meta name="robots" content="index, follow" />
		<meta name="googlebot" content="index,follow" />
		<meta name="msnbot" content="index,follow" />		
		<meta name="author" content="adilahsoft.com" />
		<meta name="copyright" content="2010 adilahsoft.com" />		
		<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js" type="text/javascript"></script> 
	</head>
	<body bgcolor="#444444" style="font-size: 9pt; font-family:Arial;">
			 <table  border="0" cellpadding="6" style="width: 100%; color: #ffffff;"> 
			 	<tr>
			 		<td align="center">::: GPS Tracker SMS :::
			 			<?php 
			 				if (isset($this->sess) && isset($this->sess->user_name)) { echo "<br />::: ".$this->sess->user_name." :: "; } ?>
			 			<?php if (isset($title)) { echo "<br />::: ".$title." :::"; } ?></td>
			 	</tr>			 
				<tr>
					<td>
						<?php echo $content; ?>
					</td>
				</tr>
			</table>
	</body>
</html>
