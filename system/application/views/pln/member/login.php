			<script>
			<!--
				function frmlogin_onsubmit()
				{
					jQuery("#wait").show();
					jQuery.post("<?=base_url();?>member/dologin", jQuery("#frmlogin").serialize(),
						function(r)
						{
							jQuery("#wait").hide();
							if (r.error)
							{
								alert(r.message);
								return;
							}
							
							location = r.redirect;		
						}
						, "json"
					);
					return false;
				}				
			-->
			</script>	
	<table height="100%" width="100%" cellpadding="0" cellspacing="0" style="font-size: 80%;">
		<tr>
			<td align="center">			
<img src='<?php echo base_url();?>assets/pln/images/banner.gif' border='0' />
				<form id="frmlogin" onsubmit="javascript: return frmlogin_onsubmit(this)">
					<table cellpadding="0" cellspacing="0" style="border: 1px solid  #cccccc; width: 780px; height: 250px; background-image: url(<?php echo base_url();?>assets/pln/images/login_fg.png); background-repeat: no-repeat;">
						<tr>
							<td><img src="<?=base_url();?>assets/images/spacer.gif" width="1" height="30" /><br /></td>
						</tr>
						<tr>
							<td align="center" valign="top"><font face="Tahoma">username</font></td>					
						</tr>
						<tr>
							<td align="center" valign="top"><input type="text" name="username" id="username" value="" class="formshort" style="width: 200px;" /></td>					
						</tr>
						<tr>
							<td><img src="<?=base_url();?>assets/images/spacer.gif" width="1" height="10" /></td>
						</tr>
						<tr>
							<td align="center" valign="top"><font face="Tahoma">password</font></td>					
						</tr>
						<tr>
							<td align="center" valign="top"><input type="password" name="userpass" id="userpass" value="" class="formshort" style="width: 200px;" /></td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>assets/images/spacer.gif" width="1" height="10" /></td>
						</tr>								
						<tr>
							<td align="center" valign="top"><div id="wait" style="display: none;"><img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" /></div><input type="submit" value=" Login " /></td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>assets/images/spacer.gif" width="1" height="30" /></td>
						</tr>			
					</table>
				</form>
			</td>		
		</tr>
	</table>			
