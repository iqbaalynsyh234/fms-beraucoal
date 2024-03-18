		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</th>
					<th width="15%" style="text-align:center;"><a href="#" onclick="javascript:order('alert_dispatcher_name')"><?if ($sortby == 'alert_dispatcher_name') { echo '<u>'; }?>Dispatcher Name<?if ($sortby == 'alert_dispatcher_name') { echo '</u>'; }?></a></th>
					<th width="10%" style="text-align:center;"><a href="#" onclick="javascript:order('alert_dispatcher_mobile')"><?if ($sortby == 'alert_dispatcher_mobile') { echo '<u>'; }?>Dispatcher Mobile<?if ($sortby == 'alert_dispatcher_mobile') { echo '</u>'; }?></a></th>
					<th width="15%" style="text-align:center;"><a href="#" onclick="javascript:order('alert_dispatcher_email')"><?if ($sortby == 'alert_dispatcher_email') { echo '<u>'; }?>Dispatcher Email<?if ($sortby == 'alert_dispatcher_email') { echo '</u>'; }?></a></th>
					<th width="5%" style="text-align:center;">Alert SMS</th>
					<th width="5%" style="text-align:center;">Alert Email</th>
					<th width="5%" style="text-align:center;">Status</th>
					<?php if ($this->sess->user_group == 0)  { ?>	
					<th width="10%" style="text-align:center;">Control</th>
					<?php } ?>	
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="center"><?=$i+1+$offset?></td>
					<td valign="center" style="text-align:center;"><?=$data[$i]->alert_dispatcher_name;?></td>
					<td valign="center"><?=$data[$i]->alert_dispatcher_mobile;?></td>
					<td valign="center"><?=$data[$i]->alert_dispatcher_email;?></td>
					
					<?php if ($data[$i]->alert_dispatcher_config_mobile == 1) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-yes.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lyes"); ?>" title="<?=$this->lang->line("lyes"); ?>"></td>
					<?php } ?>
					<?php if ($data[$i]->alert_dispatcher_config_mobile == 0) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-no.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lno"); ?>" title="<?=$this->lang->line("lno"); ?>"></td>
					<?php } ?>
					
					<?php if ($data[$i]->alert_dispatcher_config_email == 1) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-yes.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lyes"); ?>" title="<?=$this->lang->line("lyes"); ?>"></td>
					<?php } ?>
					<?php if ($data[$i]->alert_dispatcher_config_email == 0) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-no.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lno"); ?>" title="<?=$this->lang->line("lno"); ?>"></td>
					<?php } ?>
					
					<?php if ($data[$i]->alert_dispatcher_status == 1) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-yes.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lyes"); ?>" title="Active"></td>
					<?php } ?>
					<?php if ($data[$i]->alert_dispatcher_status == 0) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-no.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lno"); ?>" title="Inactive"></td>
					<?php } ?>

					<?php if ($this->sess->user_group == 0)  { ?>	
					<td valign="center" style="text-align:center;">						
						<a href="<?=base_url();?>andalas_info_alert/edit_info_alert/<?=$data[$i]->alert_dispatcher_id;?>"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-edit.png" border="0" width="20" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a> 
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->alert_dispatcher_id;?>)"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-delete.png" border="0" width="20" alt="Delete Data" title="Delete Data"></a>
					</td>
					<?php } ?>
					
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='14'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="14"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
