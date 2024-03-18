				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td width="40%"><?=$this->lang->line("lvehicle");?></td>
						<td width="1">:</td>
						<td><?php echo $vehicle->vehicle_name; ?> - <?php echo $vehicle->vehicle_no; ?></td>
					</tr>
    			<tr>
    			<tr>
						<td><?=$this->lang->line("ltransfer_method");?></td>
						<td>:</td>
						<td><?php echo $post['transfermethod']; ?></td>
					</tr>
					<?php if (isset($bank)) { ?>
    			<tr>
						<td><?=$this->lang->line("ldestination_account");?></td>
						<td width="1">:</td>
						<td>
								acc: <?php echo $bank->bank_acc; ?>
								name: <?php echo $bank->bank_name; ?>
								branch: <?php echo  $bank->bank_branch; ?>
						</td>
				</tr>
				<?php } ?>
    			<tr>
						<td><?=$this->lang->line("lpayment_amount_desc");?></td>
						<td>:</td>
						<td><?php echo $post['amount']; ?></td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("lpayment_date");?></td>
						<td>:</td>
						<td><?php echo $post['paymentdate']; ?></td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("ltransfer_code_desc");?></td>
						<td>:</td>
						<td><?php echo $post['transfercode']; ?></td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("lsendername_desc");?></td>
						<td>:</td>
						<td><?php echo $post['sendername']; ?></td>
				</tr>				
				</table>
