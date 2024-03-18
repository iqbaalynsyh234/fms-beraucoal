<form method="post" action="<?php echo base_url()."sms/dologin"; ?>">
		<table width='100%'  border="0" cellpadding="6" cellspacing="6">
				<?php if (isset($error)) { ?>
				<tr>					
					<td  align="center"><font color="#ff0000"><?php echo $errormsg; ?></font> </td>
				</tr>				
				<?php } ?>			 
				<tr>
					<td align="center">username:&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td align="center"><input type="text" name="username" value="" /></td>
				</tr>
				<tr>
					<td align="center">password:&nbsp;&nbsp;</td>
				</tr>				
				<tr>
					<td align="center"><input type="password" name="userpass" value="" /></td>
				</tr>
				<tr>					
					<td  align="center"><input type="submit" value=" Submit " /></td>
				</tr>				 
		</table>
</form>