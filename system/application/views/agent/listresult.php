		<table width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th><a href="#" onclick="javascript:order('agent_name')"><?=$this->lang->line("lagent_name"); ?></a></th>
					<th width="20%"><?=$this->lang->line("lsite"); ?></th>
					<th width="14%"><?=$this->lang->line("lpayment_total");?></th>
					<th width="30%">&nbsp;</th>					
					<th width="40px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td><?=$data[$i]->agent_name;?></td>
					<td><?=$data[$i]->agent_site;?></td>
					<td style="text-align: right;"><?php echo $data[$i]->agent_payment_periode ? sprintf("Rp %s / %s %s", number_format($data[$i]->agent_payment_amount, 0, "", ","), number_format($data[$i]->agent_payment_periode, 0, "", ","), strtolower($this->lang->line("lmonthlabel"))) : "-"; ?> </td>
					<td>
						<?php
							$info = "";
							if ($data[$i]->agent_canedit_vactive) 
							{
								$info = $this->lang->line("lcan_edit_vactivate");
							} 

							if ($data[$i]->agent_alert_pulsa) 
							{
								if (strlen($info)) 
								{
									$info .= "<br />";
								}
								$info = $this->lang->line("lsend_alert_pulsa");
							} 

							echo $info;
						?>
						&nbsp;
					</td>					
					<td>
							<a href="<?=base_url();?>agent/add/<?=$data[$i]->agent_id;?>"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
							<a href="<?=base_url();?>agent/remove/<?=$data[$i]->agent_id;?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"></a>
					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="6"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
