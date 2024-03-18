		<script>
			function changeport(vid)
			{
				if (! confirm("<?php echo $this->lang->line('lchangeport_confirm');?>")) return;
				
				jQuery.post("<?=base_url();?>user/changeport/"+vid, {},
					function(r)
					{
						alert(r.message);
						if (r.error) return;
						
						jQuery("#changeport"+vid).hide();
					}
					, "json"
				);				
			}
			</script>
		<table width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th width="12%"><a href="#" onclick="javascript:order('payment_created')"><?if ($sortby == 'payment_created') { echo '<u>'; }?><?=$this->lang->line("lpayment_date"); ?><?if ($sortby == 'payment_created') { echo '</u>'; }?></th>
					<th width="15%"><a href="#" onclick="javascript:order('vehicle_name')"><?if ($sortby == 'vehicle_name') { echo '<u>'; }?><?=$this->lang->line("lvehicle"); ?><?if ($sortby == 'vehicle_name') { echo '</u>'; }?></a></th>
					<th><?=$this->lang->line("ldescription"); ?></th>
					<th width="8%"><?=$this->lang->line("lstatus"); ?></th>
					<th width="12%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top"><?=date("d/m/Y H:i:s", $data[$i]->payment_created_fmt);?></td>
					<td valign="top"><?=$data[$i]->vehicle_name;?> <?=$data[$i]->vehicle_no;?></td>
					<td valign="top">
						<table width="100%" cellpadding="3" class="tablelist">
							<tr>
								<td width="20%" style="border: 0px;"><?php echo $this->lang->line("ltransfer_method"); ?></td>
								<td width="1%" style="border: 0px;">:</td>
								<td style="border: 0px;"><?=$data[$i]->payment_method;?></td>
							</tr>
							<tr>
								<td style="border: 0px;"><?php echo $this->lang->line("ldestination_account"); ?></td>
								<td style="border: 0px;">:</td>
								<td style="border: 0px;">No Rek.<?php echo $data[$i]->bank_branch; ?> <?php echo $data[$i]->bank_acc; ?> a/n <?php echo $data[$i]->bank_name; ?></td>
							</tr>
							<tr>
								<td style="border: 0px;"><?php echo $this->lang->line("lpayment_amount"); ?></td>
								<td style="border: 0px;">:</td>
								<td style="border: 0px;"><?php echo number_format($data[$i]->payment_amount, 0, "", "."); ?></td>
							</tr>							
							<tr>
								<td style="border: 0px;">a.n</td>
								<td style="border: 0px;">:</td>
								<td style="border: 0px;"><?php if ($data[$i]->payment_method == "cash") { echo "(".$data[$i]->payment_transfer_code.")"; } ?> <?php echo $data[$i]->payment_name; ?></td>
							</tr>							
						</table>
					</td>
					<td valign="top">
						<?php
						switch($data[$i]->payment_status)
						{
							case 1:
								echo $this->lang->line("lpending");
							break;
							case 2:
								echo $this->lang->line("lappproved");
							break;
							case 3:
								echo $this->lang->line("lcancelled");
							break;							
						}
						?>
					</td>
					<td valign="top">
						
						[ <a href="<?=base_url();?>payment/approved/<?=$data[$i]->payment_id;?>"><font color="#0000ff"><?php echo $this->lang->line("lappprove");?></font></a> ]
						[ <a href="<?=base_url();?>payment/cancelled/<?=$data[$i]->payment_id;?>"><font color="#0000ff"><?php echo $this->lang->line("lcancel");?></font></a> ]
						
					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="9"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>