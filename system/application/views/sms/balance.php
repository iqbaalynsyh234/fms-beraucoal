<table width="100%" cellpadding="6" cellspacing='0'>
	<tr bgcolor="#ffffff">
		<th width="2%" style="border-left: 1px #ffffff solid;"><font color="#000000">No</th>
		<th width="10%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lpayment_date"); ?></font></th>
		<th width="20%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lkreditor"); ?></font></th>
		<th width="10%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lamount"); ?></font></th>
		<th width="10%" style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("lstatus"); ?></font></th>
		<th style="border-left: 1px #ffffff solid;"><font color="#000000"><?php echo $this->lang->line("ldescription"); ?></font></th>
		<?php if ($this->sess->user_type == 1) { ?>
		<th width="10%">&nbsp;</td>
	<?php } ?>
	</tr>
	<?php for($i=0; $i < count($rows); $i++) { ?>
	<tr>
		<td align="right" style="border-left: 1px #ffffff solid;" valign="top"><?php echo $i+$offset+1; ?>&nbsp;</td>
		<td align="center" style="border-left: 1px #ffffff solid;" valign="top"><?php $t = dbmaketime($rows[$i]->smspayment_date); echo date("d/m/Y", $t); ?></td>
		<td align="left" style="border-left: 1px #ffffff solid;" valign="top">&nbsp;<?php echo $rows[$i]->kreditor; ?>&nbsp;</td>
		<td align="right" style="border-left: 1px #ffffff solid;" valign="top">Rp <?php echo number_format($rows[$i]->smspayment_amount, 0, "", "."); ?>&nbsp;</td>
		<td align="center" style="border-left: 1px #ffffff solid;" valign="top">
			<?php
			switch($rows[$i]->smspayment_status)
			{
				case 1:
					echo $this->lang->line("lpending");
				break;
				case 2:
					echo $this->lang->line("lapproved");
				break;
				case 3:
					echo $this->lang->line("lcancelled");
				break;				
			}
			?>
		</td>	
		<td align="left" style="border-left: 1px #ffffff solid;" valign="top">
			<?php echo $this->lang->line("ltransfer_method"); ?>: <?php echo $rows[$i]->smspayment_method; ?>
			<br /><?php echo $this->lang->line("ldestination_account"); ?>: <?php echo $rows[$i]->bank_branch; ?> <?php echo $rows[$i]->bank_acc; ?> a/n <?php echo $rows[$i]->bank_name; ?>
			<br /><?php echo $this->lang->line("ltransfer_code"); ?>: <?php echo $rows[$i]->smspayment_validation; ?>
			<br /><?php echo $this->lang->line("lsendername"); ?>: <?php echo $rows[$i]->smspayment_name; ?>
		</td>
		<?php if ($this->sess->user_type == 1) { ?>
			<?php if ($rows[$i]->smspayment_status == 1) { ?>
			<td valign="top" style="border-left: 1px #ffffff solid; border-right: 1px #ffffff solid;">[ <a href="<?php echo base_url(); ?>sms/paymentapprove/<?php echo $rows[$i]->smspayment_id; ?>" onclick="javascript: return confirm('<?php echo $this->lang->line('lsmsconfirm_approve'); ?>')">Approve</a> ] [ <a href="<?php echo base_url(); ?>sms/paymentcancel/<?php echo $rows[$i]->smspayment_id; ?>" onclick="javascript: return confirm('<?php echo $this->lang->line('lsmsconfirm_cancel'); ?>')">Cancel</a> ]</td>
			<?php } else { ?>
			<td style="border-left: 1px #ffffff solid; border-right: 1px #ffffff solid;">&nbsp;</td>
			<?php } ?>
		<?php } ?>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="<?php if ($this->sess->user_type == 1) { ?>7<?php } else { ?>6<?php } ?>" style="border: 1px #ffffff solid; border-right: 1px #ffffff solid;" align="right"><?php echo $paging; ?></td>
	</tr>
</table>
<br />
<a href='<?php echo base_url(); ?>sms/home'><font color="#aaaa00">[ <?php echo $this->lang->line('lback'); ?> ]</font></a>