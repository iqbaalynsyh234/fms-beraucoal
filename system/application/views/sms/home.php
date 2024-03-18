		<table width='100%'  border="0" cellpadding="6" cellspacing="6">	
				<?php if ($this->sess->user_type != 2) { ?>		
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/sendall"><font color="#aaaa00">[ <?php echo $this->lang->line("lsendallusers"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>			
				<?php } ?>
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/autorefill"><font color="#aaaa00">[ <?php echo $this->lang->line("lauto_refill"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>							
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/deposit"><font color="#aaaa00">[ <?php echo $this->lang->line("ldeposit"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>				
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/payment"><font color="#aaaa00">[ <?php echo $this->lang->line("lreport_payment"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>				
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/balance"><font color="#aaaa00">[ <?php echo $this->lang->line("lbalance"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>			
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/contactus"><font color="#aaaa00">[ <?php echo $this->lang->line("lcontactus"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>			
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/help"><font color="#aaaa00">[ <?php echo $this->lang->line("lhelp"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>				
				<tr>
					<td align="center"><a href="<?php echo base_url(); ?>sms/logout"><font color="#aaaa00">[ <?php echo $this->lang->line("llogout"); ?> ]</font></a>&nbsp;&nbsp;</td>
				</tr>					
		</table>
