<table width="100%" cellpadding="6" cellspacing='0'>
	<tr bgcolor="#ffffff">
		<th width="2%" style="border-left: 1px #ffffff solid;"><font color="#000000">No</th>
		<th style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lagent"); ?></font></th>
		<th width="30%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lperiod"); ?></font></th>
	</tr>
	<?php for($i=0; $i < count($rows); $i++) { ?>
	<tr>
		<td align="right" style="border-left: 1px #ffffff solid; border-bottom: 1px #ffffff solid;" valign="top"><?php echo $i+1; ?>&nbsp;</td>
		<td align="left" style="border-left: 1px #ffffff solid; border-bottom: 1px #ffffff solid;" valign="top">&nbsp;<?php echo $rows[$i]->agent_name; ?>&nbsp;</td>
		<td align="center" style="border-left: 1px #ffffff solid;border-right: 1px #ffffff solid; border-bottom: 1px #ffffff solid;" valign="top">
			<?php 
				if ((! $rows[$i]->agent_smsactive1) || ($rows[$i]->agent_smsactive1 == "0000-00-00 00:00:00"))
				{
					echo $this->lang->line("linactive");
				}
				else
				{
					$t1 = dbmaketime($rows[$i]->agent_smsactive1); 
					$t2 = dbmaketime($rows[$i]->agent_smsactive2); 
					
					echo date("d/m/Y", $t1)." - ".date("d/m/Y", $t2);
				}
			?>
		</td>		
	</tr>
	<?php } ?>
</table>
<br />
<a href='<?php echo base_url(); ?>sms/home'><font color="#aaaa00">[ <?php echo $this->lang->line('lback'); ?> ]</font></a>