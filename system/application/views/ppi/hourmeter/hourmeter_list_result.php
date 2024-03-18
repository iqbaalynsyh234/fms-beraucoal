		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="10%" valign="top" style="text-align:center;">Vehicle No</th>
					<th width="12%" valign="top" style="text-align:center;">Vehicle Name</th>
					<th width="8%" valign="top" style="text-align:center;">Hourmeter (HM)</th>
					<th width="8%" valign="top" style="text-align:center;">Date HM</th>
					<th width="8%" valign="top" style="text-align:center;">Last Service</th>
					<th width="8%" valign="top" style="text-align:center;">Last Service HM</th>
					<th width="8%" valign="top" style="text-align:center;">Note</th>
					<th width="5%" valign="top" style="text-align:center;">Control</th>
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
					<td valign="top" style="text-align:left;">
						<?=$data[$i]->data_hm_vehicle_no; ?>
					</td>
					<td valign="top" style="text-align:left;">
						<?=$data[$i]->data_hm_vehicle_name; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->data_hm_value; ?> Hours
					</td>
					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y H:i",strtotime($data[$i]->data_hm_datetime)) ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y",strtotime($data[$i]->data_hm_last_service)) ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->data_hm_last_service_string; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->data_hm_note; ?>
					</td>
					<?php if ($this->sess->user_group == 0) { ?>
						<td valign="top" style="text-align:center;">
							<a href="<?=base_url();?>transporter/ppi_hourmeter/edit_hourmeter/<?=$data[$i]->data_hm_id;?>"> <img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="Edit Data" title="Edit Data">
							</a>
							
							<a href="#" onclick="javascript:delete_data(<?=$data[$i]->data_hm_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="Delete Data" title="Delete Data"></a>
							
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
