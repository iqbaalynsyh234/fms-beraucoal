<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center;">No.</td>
					<th style="text-align:center;">Geofence Name</th>
					<th style="text-align:center;">Vehicle Device</th>
					<th style="text-align:center;">Created</th>
					<!--<th style="text-align:center;">Type</th>-->
					<th style="text-align:center;">Control</th>
				</tr>
			</thead>
			<tbody>
			<?php
			
			if(count($data) > 0){
			
			for($i=0; $i < count($data); $i++)
			{
			
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top" align="center" style="text-align:center;"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->geofence_name;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->geofence_vehicle;?></td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->geofence_user;?><br />
						<?=date("d-m-Y H:i:s", strtotime($data[$i]->geofence_created));?>
					</td>
					<!--<td valign="top" style="text-align:center;">
					<?php if($$data[$i]->geofence_type == "cust"){  ?>
						Customer
					<?php }else{ ?>
						<?=$data[$i]->geofence_type;?>
					<?php } ?>
					</td>-->
					<?php if ($this->sess->user_group == 0 ){ ?>
					<td valign="top" style="text-align:center;">
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->geofence_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0" width="20" alt="Delete Data" title="Delete Data"></a>
					</td>
					<?php } ?>
					
				</tr>
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="10">No Available Data</td></tr>
			<?php } ?>
			</tbody>
			<tfoot>
					<tr>
						
						<td colspan="10"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
