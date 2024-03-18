		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="10%" valign="top" style="text-align:center;">Vehicle</th>
					<th width="8%" valign="top" style="text-align:center;">Driver</th>
					<th width="8%" valign="top" style="text-align:center;">Datetime</th>
					<th width="8%" valign="top" style="text-align:center;">Muatan</th>
					<th width="8%" valign="top" style="text-align:center;">Destination</th>
					<th width="8%" valign="top" style="text-align:center;">Notes</th>
					<th width="8%" valign="top" style="text-align:center;">Control</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->muatan_vehicle_no; ?><br /><?=$data[$i]->muatan_vehicle_name; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->driver_name; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y H:i",strtotime($data[$i]->muatan_startdate. " ".$data[$i]->muatan_starttime)) ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->muatan_data_name; ?><br /><?=$data[$i]->muatan_weight; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->muatan_dest; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->muatan_note; ?>
					</td>
					
					<?php if ($this->sess->user_group == 0) { ?>
						<td valign="top" style="text-align:center;">
							<a href="<?=base_url();?>bgn_muatan/edit_muatan/<?=$data[$i]->muatan_id;?>"> <img src="<?=base_url();?>assets/newfarrasindo/images/icon-edit.png" border="0" width="20" alt="Edit Data" title="Edit Data"></a>
							<a href="#" onclick="javascript:delete_data(<?=$data[$i]->muatan_id;?>)"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-delete.png" border="0" width="20" alt="Delete Data" title="Delete Data"></a>
						</td>
					<?php } ?>
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
