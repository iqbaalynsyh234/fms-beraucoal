<table width="100%" cellpadding="6" cellspacing='0'>
	<tr bgcolor="#ffffff">
		<th width="2%" style="border-left: 1px #ffffff solid;"><font color="#000000">No</th>
		<th width="10%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("ldate"); ?></font></th>
		<th width="20%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("ldebet"); ?></font></th>
		<th width="20%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lcredit"); ?></font></th>
		<th width="20%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lsaldo"); ?></font></th>
		<th style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("ldescription"); ?></font></th>
	</tr>
	<?php for($i=0; $i < count($rows); $i++) { ?>
	<tr>
		<td align="right" style="border-left: 1px #ffffff solid;" valign="top"><?php echo $i+$offset+1; ?>&nbsp;</td>
		<td align="center" style="border-left: 1px #ffffff solid;" valign="top"><?php $t = dbmaketime($rows[$i]->smsbalance_created); echo date("d/m/Y", $t); ?></td>
		<td align="right" style="border-left: 1px #ffffff solid;" valign="top">&nbsp;<?php echo ($rows[$i]->smsbalance_debet) ? number_format($rows[$i]->smsbalance_debet, "", "", ".") : ""; ?>&nbsp;</td>
		<td align="right" style="border-left: 1px #ffffff solid;" valign="top">&nbsp;<?php echo ($rows[$i]->smsbalance_kredit) ? number_format($rows[$i]->smsbalance_kredit, "", "", ".") : ""; ?>&nbsp;</td>
		<td align="right" style="border-left: 1px #ffffff solid;" valign="top">&nbsp;<?php echo number_format($rows[$i]->smsbalance_saldo, "", "", "."); ?>&nbsp;</td>
		<td align="left" style="border-left: 1px #ffffff solid;border-right: 1px #ffffff solid;" valign="top">&nbsp;<?php echo $rows[$i]->smsbalance_desc; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="6" style="border: 1px #ffffff solid; border-right: 1px #ffffff solid;" align="right"><?php echo $paging; ?></td>
	</tr>
</table>
<br />
<a href='<?php echo base_url(); ?>sms/home'><font color="#aaaa00">[ <?php echo $this->lang->line('lback'); ?> ]</font></a>