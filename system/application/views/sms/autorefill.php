			<form name="frmautorefill" id="frmautorefill" method="post" action="<?php echo base_url(); ?>sms/saveautorefill">				
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
							case "ve":
								echo $this->lang->line("lempty_vehicle_list");
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
									<?php echo $this->lang->line("lsetting_saved"); ?>
								</font>
							</td>
						</tr>			 		
			 		<?php			 			
			 		}
			 ?>
			 	<tr>
			 		<td valign="top" colspan="2">&nbsp;</td>
			 		<td colspan="3"><?=$this->lang->line("lauto_refill_desc");?></td>
			 	</tr>
    			<tr>
					<td valign="top" width="40%" align="right"><?=$this->lang->line("lvehicle_no");?></td>
					<td valign="top" width="1">:</td>
					<td valign="top">
						<?php if (count($vehicles) > 1) { ?>
							<?php if ($centangall) { ?>
							<a href="<?php echo base_url(); ?>sms/autorefill/"><?php echo  $this->lang->line("lnot_check_all"); ?></a><br />
							<input type="hidden" name="centangsemua" value="1" />
							<?php } else { ?>
							<a href="<?php echo base_url(); ?>sms/autorefill/centangsemua"><?php echo  $this->lang->line("lcheck_all"); ?></a><br />
							<input type="hidden" name="centangsemua" value="0" />
							<?php } ?>
						<?php } ?>
						<?php for($i=0; $i < count($vehicles); $i++) { ?>
						<input type="checkbox" id="vehicle[]" name="vehicle[]" value="<?php echo $vehicles[$i]->vehicle_id; ?>"<?php if ($centangall || $vehicles[$i]->vehicle_autorefill) { echo " checked"; } ?> /> <?php echo $vehicles[$i]->vehicle_no; ?> <?php echo $vehicles[$i]->vehicle_name; ?> (<?php echo $vehicles[$i]->vehicle_card_no; ?>)<br />
						<?php } ?>
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
