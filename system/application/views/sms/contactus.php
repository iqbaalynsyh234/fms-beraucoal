			<form name="frmcontactus" id="frmcontactus" method="post" action="<?php echo base_url(); ?>sms/savecontactus">				
				<table width="100%" cellpadding="3" class="tablelist">
			<?php 	if ($flag == "e") 			
					{ 
						?>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td>
								<font color="#ff0000">
						<?php
						switch($arg)
						{							
							case "co":
								echo $this->lang->line("lempty_message");
							break;							
						}
						?>
								</font>
								<br />&nbsp;
							</td>
						<?php
			 		}
			 		else 
			 		if ($flag == "s")
			 		{
			 		?>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td>
								<font color="#ffffff">
									<?php echo $this->lang->line("lcontactus_success"); ?>
								</font>
							</td>
						</tr>			 		
			 		<?php			 			
			 		}
			 ?>
    			<tr>
					<td valign="top" width="40%" align="right"><?=$this->lang->line("lmessage");?></td>
					<td valign="top" width="1">:</td>
					<td valign="top">
						Sertakan no hp Anda. Kami akan memberikan informasi via sms.
						<textarea id="msg" name="msg" rows="4" cols="50"></textarea>
					</td>
				</tr>
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="btnsave" id="btnsave" value=" Send " />
						</td>
					</tr>					
				</table>
			</form>		
