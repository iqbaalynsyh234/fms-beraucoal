<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Armada PLN Tasikmalaya Tracker</title>
		<meta name="description" content="<?=isset($metakeywords) ? $metakeywords : $this->config->item("APPDESCRIPTION"); ?>" />
		<meta name="keywords" content="<?=isset($metadescription) ? $metadescription : $this->config->item("APPKEYWORDS"); ?>" />
		<meta name="robots" content="index, follow" />
		<meta name="googlebot" content="index,follow" />
		<meta name="msnbot" content="index,follow" />		
		<meta name="author" content="adilahsoft.com" />
		<meta name="copyright" content="2010 adilahsoft.com" />
		<style type="text/css" media="screen">@import url(<?=base_url();?>assets/css/default.css);</style>
		<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js" type="text/javascript"></script> 
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
