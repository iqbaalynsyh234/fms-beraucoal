<style>
	.table tr.willexpired td{
		background-color:#FAFA50;
	}
	.table tr.expired td{
		background-color:#FF6347;
		font-color:#FFFFFF;
		
	}
</style>

		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="10%" valign="top" style="text-align:center;">Vehicle No</th>
					<th width="10%" valign="top" style="text-align:center;">Vehicle Name</th>
					<th width="10%" valign="top" style="text-align:center;">Last Service HM</th>
					<th width="10%" valign="top" style="text-align:center;">Last Update HM</th>
					<th width="5%" valign="top" style="text-align:center;">Status</th>
					<th width="8%" valign="top" style="text-align:center;">Control</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr 
				<?php 
				if($data[$i]->hm_alert_status == 1){ 
					echo "class='willexpired'";
				}elseif($data[$i]->hm_alert_status == 2){
					echo "class='expired'";
				}				
				?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:left;">
						<?=$data[$i]->data_hm_vehicle_no; ?>
					</td>
					<td valign="top" style="text-align:left;">
						<?=$data[$i]->data_hm_vehicle_name; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<small><?=date("d-m-Y",strtotime($data[$i]->data_hm_last_service)) ?></small><br />
						<?=$data[$i]->data_hm_last_service_value; ?> hours
					</td>
					
					<td valign="top" style="text-align:center;">
						<small><?=date("d-m-Y",strtotime($data[$i]->hm_alert_datetime)) ?></small><br />
						<?=$data[$i]->hm_alert_string; ?>
					</td>

					
					<td valign="top" style="text-align:center;">
						<?php 
							if ($data[$i]->hm_alert_status == 0){ ?>
								<img src="<?=base_url();?>assets/images/warning_green.png" border="0" alt="" title="" height="16" width="16">
						<?php } ?>
						<?php 
							if ($data[$i]->hm_alert_status == 1){ ?>
								<img src="<?=base_url();?>assets/images/warning_yellow.png" border="0" alt="" title="" height="16" width="16">
						<?php } ?>
						
						<?php 
							if ($data[$i]->hm_alert_status == 2){ ?>
								<img src="<?=base_url();?>assets/images/warning_red.png" border="0" alt="" title="" height="16" width="16">
						<?php } ?>
					</td>
					
					<td valign="top" style="text-align:center;">
						<a href="<?=base_url();?>transporter/ppi_hourmeter/edit_alert/<?=$data[$i]->hm_alert_vehicle_id;?>"> 
							<img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="Update Last Service" title="Update Last Service">
						</a>
					</td>
					
				</tr>
				<tr>
					<td colspan="12">
						
					</td>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='12'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
						<td colspan="12"><!--<?=$paging?>--></td>
					</tr>
			</tfoot>
		</table>
