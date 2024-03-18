<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>www.lacak-mobil.com :: Announcement System</title>
</head>

<body style="padding-right: 0px; padding-left: 0px; padding-bottom: 0px; margin: 0px; padding-top: 0px; font-size: 9pt; font-family:Arial;">
	<div style="left:0px;top:0px;height:320px;width:240px;position:absolute;zIndex:1;">
		<form action="<?php echo base_url(); ?>announcement/mailform/<?php echo date("Ymd");?>/bismillaah" method="post">
			<table cellpadding="0" cellspacing="0" style="width: 240px; text-align: center" >
				<tr>
					<td>www.lacak-mobil.com</td>
				</tr>
				<tr>
					<td>Announcement System</td>
				</tr>		
			</table>
			<br />
		<?php if (isset($errmessage)) { ?>
			<font color="#ff0000">
				<?php 
				foreach($errmessage as $val)
				{
					echo $val."<br />";
				}
				?>
			</font>
		<?php }	?>
		<?php 
			if ($this->uri->segment(5) == "success") { ?>
			<font color="#000000">
				<h3>email sent</h3>
			</font>
		<?php }	?>		
			<br />
	    	<table cellpadding="0" cellspacing="0" style="width: 240px;" >
	        	<tr>
	            	<td valign="top">Subject&nbsp;&nbsp;</td>            		
	        		<td valign="top"><input type="text" name="subject" value="" /></td>            		
	        	</tr>
	        	<tr>
	            	<td valign="top">Message&nbsp;&nbsp;</td>            		
	        		<td valign="top"><textarea name="message" rows="6" cols="30"></textarea></td>            		
	        	</tr>
	        	<tr>
	        		<td valign="top">&nbsp;&nbsp;</td>            		
	        		<td valign="top"><input type="submit" value="send" /></td>            		
	        	</tr>
			</table>
		</form>
	</div>
</body>
</html>
