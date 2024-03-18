		<?php echo $this->lang->line("ltotal_invoice"); ?>: <?php echo $totalinvoices; ?> / Rp. <?php echo number_format($totalamount, 0, "", "."); ?>
		<table width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<!--<th width="2%">No.</td>-->
					<th width="100px;" style="text-align: center"><?=$this->lang->line("linvoice_no"); ?></th>
					<?php if ($this->sess->user_type != 2) { ?>
					<th width="10%"><?=$this->lang->line("lusername"); ?></th>
					<?php } ?>
					<th width="10%"><?=$this->lang->line("lvehicle"); ?></th>
					<th width="8%"><?=$this->lang->line("lprinted"); ?></th>
					<th width="8%"><?=$this->lang->line("ldate_maturity"); ?></th>					
					<th width="10%"><?=$this->lang->line("lamount"); ?></th>
					<th><?=$this->lang->line("lstatus"); ?></th>
					<th width="4%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($invoices); $i++)
			{
				$t_printed = dbmaketime($invoices[$i]->invoice_date);
				$t_maturity = dbmaketime($invoices[$i]->invoice_period1);
				
				switch($invoices[$i]->invoice_status)
				{
					case 1:
						$status = sprintf("%s | <a href='javascript:confirmation(\"%s\")'><font color='#0000ff'>%s</font></a>", $this->lang->line("lnot_payment"), $invoices[$i]->invoice_no, $this->lang->line("lconfirmation"));
					break;
					case 2:
						$status = $this->lang->line("lproessed"); 
					break;
					case 3:
						$status = $this->lang->line("lpaid");
					break;				
					case 4:
						$status = $this->lang->line("lrejected");
					break;	

				}
				
				if ($invoices[$i]->invoice_status != 1)
				{
					if (isset($invoices[$i]->payments) && count($invoices[$i]->payments)) 
					{
						$status .= sprintf(" | <a href='javascript:payment(%d)'><font color='#0000ff'>%s <span id='plusminus%d'>(+)</span></font></a>", $invoices[$i]->invoice_id, $this->lang->line("ltransaction"), $invoices[$i]->invoice_id);
					}
				}
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<!--<td><?=$i+1+$offset?></td>-->
					<td style="text-align: right;"><?=$invoices[$i]->invoice_no?></td>
					<?php if ($this->sess->user_type != 2) { ?>
					<td><?php echo $invoices[$i]->user_name; ?></td>
					<?php } ?>
					<td>
						<?php 
							if ((! isset($invoices[$i]->vehicles)) || (count($invoices[$i]->vehicles) == 0))
							{
								echo "ALL";
								$amount = $invoices[$i]->invoice_amount;
							}
							else
							{
								$j = 0; 
								foreach ($invoices[$i]->vehicles as $vehicle) 
								{
									if ($j > 0) { echo ", "; }								
									echo $vehicle->vehicle_no;
									$j++;
								}
								
								$amount = $invoices[$i]->invoice_amount*count($invoices[$i]->vehicles);
							}
						?>
					</td>
					<td style="text-align: center;"><?=date("d/m/Y", $t_printed)?></td>
					<td style="text-align: center;"><?=date("d/m/Y", $t_maturity)?></td>
					<td style="text-align: right;">Rp. <?=number_format($amount, 0, "", ".")?></td>
					<td><?=$status?></td>
					<td><a href="<?php printf("%sinvoice/show/%d", base_url(), $invoices[$i]->invoice_id); ?>" target="_blank"><font color="#0000ff"><?php echo $this->lang->line("lopen"); ?></font></a></td>
				</tr>
				<?php if (isset($invoices[$i]->payments) && count($invoices[$i]->payments)) { ?>
				<tr id="trpayment<?php echo $invoices[$i]->invoice_id; ?>" style="display: none;">
					<td colspan="<?php echo ($this->sess->user_type != 2) ? 8 : 7;?>">
					<table width="100%" cellpadding="2" cellspacing="2" border="0" class="tablelist" bgcolor="#eeeeee">
				<?php $j = 0; foreach($invoices[$i]->payments as $payment) { ?>
				<?php 
					$t = dbmaketime($payment->payment_date); 
					
					switch($payment->payment_status)
					{
						case 1:
							if (($this->sess->user_type == 1) || ($this->sess->user_type == 4))
							{
								$status = sprintf("%s | <a href='javascript:approved(%d)'><font color='#0000ff'>%s</font></a> | <a href='javascript:rejected(%d)'><font color='#0000ff'>%s</font></a>", $this->lang->line("lnew"), $payment->payment_id, $this->lang->line("lapproved"), $payment->payment_id, $this->lang->line("lrejected"));
							}
							else
							{
								$status = $this->lang->line("lnew");
							}
						break;
						case 2:
							$status = $this->lang->line("lapproved");
						break;
						case 3:
							$status = $this->lang->line("lrejected");
						break;

					}
				?>
				<tr>
					<td colspan="3"><?php echo $this->lang->line("lpayment"); ?> <?php echo ++$j; ?></td>
				</tr>
				<tr>
					<td width="20%"><?php echo $this->lang->line("ltransfer_method"); ?></td>
					<td width="5">:</td>
					<td><?php echo $payment->payment_method; ?></td>
				</tr>
				<tr>
					<td><?php echo $this->lang->line("ldestination_account"); ?></td>
					<td>:</td>
					<td><?php echo $payment->bank_branch; ?> <?php echo $payment->bank_acc; ?> a/n <?php echo $payment->bank_name; ?></td>
				</tr>
				<tr>
					<td><?php echo $this->lang->line("lamount"); ?></td>
					<td>:</td>
					<td><?php echo number_format($payment->payment_amount, 0, "", ","); ?></td>
				</tr>
				<tr>
					<td><?php echo $this->lang->line("ldate"); ?></td>
					<td>:</td>
					<td><?php echo date("d/m/Y", $t); ?></td>
				</tr>
				<tr>
					<td><?php echo $this->lang->line("ltransfer_code"); ?></td>
					<td>:</td>
					<td><?php echo $payment->payment_transfer_code; ?></td>
				</tr>
				<tr>
					<td><?php echo $this->lang->line("lsendername"); ?></td>
					<td>:</td>
					<td><?php echo $payment->payment_name; ?></td>
				</tr>
				<tr>
					<td><?php echo $this->lang->line("lstatus"); ?></td>
					<td>:</td>
					<td><?php echo $status; ?></td>
				</tr>				
				<?php } ?>
					</table>
					</td>
				</tr>
				<?php } ?>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="<?php echo ($this->sess->user_type != 2) ? 8 : 7;?>"><?php echo $paging;?></td>
					</tr>
			</tfoot>
		</table>
