		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="10%" valign="top" style="text-align:center;">Job Number</th>
					<th width="10%" valign="top" style="text-align:center;">No.PO</th>
					<th width="10%" valign="top" style="text-align:center;">Vehicle</th>
					<th width="8%" valign="top" style="text-align:center;">Driver</th>
					<th width="8%" valign="top" style="text-align:center;">Area</th>
					<th width="8%" valign="top" style="text-align:center;">Datetime</th>
					<th width="8%" valign="top" style="text-align:center;">Start From</th>
					<th width="8%" valign="top" style="text-align:center;">Destination</th>
					<th width="8%" valign="top" style="text-align:center;">Client</th>
					<th width="8%" valign="top" style="text-align:center;">Dimension</th>
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
						<?=$data[$i]->transporter_job_number; ?>
						<?php if ($data[$i]->transporter_job_status == 2) { ?>
						<small>
							<br />
							<?php echo "Delivered"; ?>
							<br />
							<?=date("d-m-Y H:i",strtotime($data[$i]->transporter_job_deliv_date." ".$data[$i]->transporter_job_deliv_time)) ?>
						</small>
						<?php } ?>
						
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->transporter_job_po; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->transporter_job_vehicle_name; ?><br /><?=$data[$i]->transporter_job_vehicle_no; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->driver_name; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->transporter_job_area; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y H:i",strtotime($data[$i]->transporter_job_date. " ".$data[$i]->transporter_job_time)) ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->transporter_job_from; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->transporter_job_to; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->transporter_job_client; ?>
					</td>
					<td valign="top" style="text-align:center;"><small>
						<?=$data[$i]->transporter_job_dimensi_p; ?>cm x <?=$data[$i]->transporter_job_dimensi_l;?>cm x <?=$data[$i]->transporter_job_dimensi_t; ?>cm <br />
						<?=$data[$i]->transporter_job_weight; ?> kg</small>
					</td>
					<?php if ($this->sess->user_group == 0) { ?>
						<td valign="top" style="text-align:center;">
							<a href="<?=base_url();?>tcont_jobfile/edit_jobfile/<?=$data[$i]->transporter_job_id;?>"> <img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="Edit Data" title="Edit Data">
							</a>
							
							<a href="#" onclick="javascript:delete_data(<?=$data[$i]->transporter_job_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="Delete Data" title="Delete Data"></a>
							<a href="#" onclick="javascript:set_delivered(<?=$data[$i]->transporter_job_id;?>)"><img src="<?=base_url();?>assets/transporter/images/delivered.png" border="0" width="18" alt="Set to Delivered" title="Set to Delivered"></a>
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
