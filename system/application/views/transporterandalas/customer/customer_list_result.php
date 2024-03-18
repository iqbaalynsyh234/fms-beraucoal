		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</th>
					<th width="15%" style="text-align:center;"><a href="#" onclick="javascript:order('customer_name')"><?if ($sortby == 'customer_name') { echo '<u>'; }?>Customer Name<?if ($sortby == 'customer_name') { echo '</u>'; }?></a></th>
					<th width="15%" style="text-align:center;">Email</th>
					<th width="15%" style="text-align:center;">Phone</th>
					<th width="15%" style="text-align:center;">Company</th>
					<th width="15%" style="text-align:center;">Alert Email</th>
					<th width="15%" style="text-align:center;">Alert SMS</th>
					<th width="10%" style="text-align:center;">Status</th>
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
					<td valign="center" style="text-align:center;"><?=$data[$i]->customer_name;?><br /><small><?=$data[$i]->customer_sex;?></small></td>
					<td valign="center" style="text-align:center;"><?=$data[$i]->customer_email;?></td>
					<td valign="center" style="text-align:left;"><?=$data[$i]->customer_phone;?><br/><?=$data[$i]->customer_mobile;?></td>
					<td valign="center" style="text-align:center;"><?=$data[$i]->customer_company_name;?></td>
					
					<?php if ($data[$i]->customer_alert_email == 1) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-yes.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lyes"); ?>" title="Yes"></td>
					<?php } ?>
					<?php if ($data[$i]->customer_alert_email == 0) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-no.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lno"); ?>" title="No"></td>
					<?php } ?>
					<?php if ($data[$i]->customer_alert_sms == 1) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-yes.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lyes"); ?>" title="Yes"></td>
					<?php } ?>
					<?php if ($data[$i]->customer_alert_sms == 0) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-no.png" height="20" width="20" border="0" alt="<?=$this->lang->line("lno"); ?>" title="No"></td>
					<?php } ?>
					<?php if ($data[$i]->customer_status == 1) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-yes.png" height="20" width="20" border="0" alt="Active" title="Active"></td>
					<?php } ?>
					<?php if ($data[$i]->customer_status == 0) { ?>
						<td valign="center" style="text-align:center;"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-no.png" height="20" width="20" border="0" alt="Inactive" title="Inactive"></td>
					<?php } ?>
					<?php if ($this->sess->user_group == 0)  { ?>	
					<td valign="center" style="text-align:center;">						
						<a href="<?=base_url();?>andalas_customer/edit/<?=$data[$i]->customer_id;?>"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-edit.png" border="0" width="20" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a> 
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->customer_id;?>)"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-delete.png" border="0" width="20" alt="Delete Data" title="Delete Data"></a>
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
