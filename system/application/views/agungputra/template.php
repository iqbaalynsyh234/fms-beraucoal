<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Monitoring Vehicle Division</title>
		<meta name="description" content="<?=$this->config->item("APPDESCRIPTION"); ?>" />
		<meta name="keywords" content="<?=$this->config->item("APPKEYWORDS"); ?>" />
                <link href="<?=base_url();?>assets/agungputra/favicon.ico" type="image/x-icon" rel="icon" />
                <link href="<?=base_url();?>assets/agungputra/favicon.ico" type="image/x-icon" rel="shortcut icon" />     
                

		<style type="text/css" media="screen">@import url(<?=base_url();?>assets/css/default.css);</style>
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script> 
	</head>
	<body style="margin-left: 0px; margin-right: 0px;">
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
    				<td height="1" align="left">
    					<?=isset($navigation)?$navigation:'&nbsp;';?>
					</td>
  			</tr>
  			<tr>
    				<td valign="top">
    					<?=$content?>
    				</td>
  			</tr>
  			<tr>
    				<td height="1">    				
					<div class="lite" id="copy">
    						Powered by <a href="http://www.adilahsoft.com" title="adilahsoft" target="_blank">adilahsoft</a>. 
    						3.0
    					<br />
  					&copy; Copyright 2010 adilahsoft. All rights reserved. </div>
				</td>
  			</tr>
		</table>
		<div id="container"></div>
	</body>
</html>
