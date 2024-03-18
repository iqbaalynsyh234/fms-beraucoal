			<script>
				function msg_onkeydown(e)
				{					
					var q = 160;					
					var c = $("#msg").val().length;					
					var r = q-c;
					if (r < 0) 
					{
						if (e.keyCode < 32) return true;
						if (e.keyCode > 126) return true;
						
						return false;
					}
					
					$("#nremain").html(r);					
					return true;
				}
				</script>
			<form name="frmsendall" id="frmsendall" method="post" action="<?php echo base_url(); ?>sms/sendusers">				
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
							case "ms":
								echo $this->lang->line("lempty_message");
							break;							
							case "de":
								echo $this->lang->line("lempty_destination");
							break;
							case "ma":
								echo $this->lang->line("lsend_failed");
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
									<?php echo $this->lang->line("lsend_success"); ?>
								</font>
							</td>
						</tr>			 		
			 		<?php			 			
			 		}
			 ?>
					<?php if ($this->sess->user_type == 1) { ?>
				<tr>
    				<td valign="top" align="right"><?=$this->lang->line("lsendto");?></td>
    				<td valign="top" width="1">:</td>
    				<td valign="top">
    					<select name="sendto" id="sendto">
    						<option value="">--<?php echo $this->lang->line("lall_user"); ?> ---</option>
    						<?php for($i=0;$i < count($agents); $i++) { ?>
    						<option value="<?php echo $agents[$i]->agent_id; ?>"><?php echo $agents[$i]->agent_name; ?></option>
    						<?php } ?>
    					</select>
    				</td>
    			</tr>
    			<?php } ?>			 
    			<tr>
					<td valign="top" width="40%" align="right"><?=$this->lang->line("lmessage");?></td>
					<td valign="top" width="1">:</td>
					<td valign="top">
						<div>
							<?=$this->lang->line("lchar_remain");?>: <span id="nremain">160</span>
						</div>
						<textarea id="msg" name="msg" rows="4" cols="50" onkeydown="javascript: return msg_onkeydown(event)"></textarea>
					</td>
				</tr>
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="btnsave" id="btnsave" value=" Send " />
						</td>
					</tr>					
				<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					<td><a href='<?php echo base_url(); ?>sms/home'><font color="#aaaa00">[ <?php echo $this->lang->line('lback'); ?> ]</font></a></td>
				</tr>
				</table>
			</form>		
