			<form name="frmpayment" id="frmpayment" method="post" action="<?php echo base_url(); ?>sms/savedeposit">				
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
							case "eba":
								echo $this->lang->line("lsms_empty_bankdest");
							break;							
							case "eam":
								echo $this->lang->line("lsms_empty_amount");
							break;
							case "iam":
								echo $this->lang->line("lsms_invalid_amount");
							break;
							case "eca":
								echo $this->lang->line("ltransfer_code_desc");
							break;
							case "enca":
								echo $this->lang->line("ltransfer_code_desc");
							break;
							case "ese":
								echo $this->lang->line("lsendername_desc");
							break;
							case "ese":
								echo $this->lang->line("lsendername_desc");
							break;							
						}
						?>
								</font>
								<br />&nbsp;
							</td>
						<?php
			 		} 
			 ?>
			 <?php if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->agent_pascabayar != 1))) { ?>
			 	<tr>
			 		<td valign="top"><?=$this->lang->line("luser");?></td>
			 		<td valign="top">:</td>
			 		<td valign="top">
			 			<select id="user" name="user">
			 				<?php for($i=0; $i < count($users); $i++) { ?>
			 				<option value="<?php echo $users[$i]->user_id; ?>"><?php echo $users[$i]->user_name; ?></option>
			 				<?php } ?>
			 			</select>
			 		</td>
			 	</tr>
			 <?php } ?>
    			<tr>
						<td valign="top" width="40%"><?=$this->lang->line("ltransfer_method");?></td>
						<td valign="top" width="1">:</td>
						<td valign="top">
							<select id="transfermethod" name="transfermethod">
								<option value="cash"><?=$this->lang->line("lcash");?></option>
								<option value="atm"><?=$this->lang->line("latm");?></option>
								<option value="internet"><?=$this->lang->line("linet_banking");?></option>
								<option value="sms"><?=$this->lang->line("lsms_banking");?></option>
							</select>
						</td>
					</tr>
    			<tr>
						<td valign="top"><?=$this->lang->line("ldestination_account");?></td>
						<td width="1" valign="top">:</td>
						<td valign="top">							
								<?php for($i=0; $i < count($banks); $i++) { ?>
								<?php if ($i > 0) { echo "<br />"; } ?>
								<input type="radio" name="bankdest" id="bankdest" value="<?php echo $banks[$i]->bank_id; ?>" />
								No Rek.<?php echo $banks[$i]->bank_branch; ?> <?php echo $banks[$i]->bank_acc; ?> a/n <?php echo $banks[$i]->bank_name; ?>
								<?php } ?>
						</td>
				</tr>
    			<tr>
						<td valign="top"><?=$this->lang->line("lsmspayment_amount_desc");?></td>
						<td valign="top">:</td>
						<td valign="top"><input type="text" name="amount" id="amount" value="X" class="formshort" /></td>
				</tr>
    			<tr>
						<td valign="top"><?=$this->lang->line("lpayment_date");?></td>
						<td valign="top">:</td>
						<td valign="top"><input type='text' name="paymentdate" id="paymentdate"  class="date-pick" value="<?php echo date("d/m/Y"); ?>"  maxlength='10'></td>
				</tr>
    			<tr>
						<td valign="top"><?=$this->lang->line("ltransfer_code_desc");?></td>
						<td valign="top">:</td>
						<td valign="top"><input type='text' name="transfercode" id="transfercode"  class="formshort" value=""></td>
				</tr>
    			<tr>
						<td valign="top"><?=$this->lang->line("lsendername_desc");?></td>
						<td valign="top">:</td>
						<td valign="top"><input type='text' name="sendername" id="sendername"  class="formdefault" value=""></td>
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
