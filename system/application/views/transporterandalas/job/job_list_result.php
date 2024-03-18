		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="8%" valign="top" style="text-align:center;">Job Number</th>
					<th width="8%" valign="top" style="text-align:center;">No.PO</th>
					<th width="8%" valign="top" style="text-align:center;">Vehicle</th>
					<th width="8%" valign="top" style="text-align:center;">Schedule Date</th>
					<th width="8%" valign="top" style="text-align:center;">Start From</th>
					<th width="10%" valign="top" style="text-align:center;">Destination</th>
					<th width="8%" valign="top" style="text-align:center;">Customer</th>
					<th width="8%" valign="top" style="text-align:center;">Items</th>
					<th width="10%" valign="top" style="text-align:center;">Dimension</th>
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
						<?=$data[$i]->job_number; ?>
						<?php if ($data[$i]->job_status == 2) { ?>
						<small>
							<br />
							<?php echo " ( Delivered ) "; ?>
							<br />
							<?=date("d-m-Y H:i",strtotime($data[$i]->job_deliv_date." ".$data[$i]->job_deliv_time)) ?>
						</small>
						<?php } ?>
						
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->job_po; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->job_mobil_name; ?><br /><?=$data[$i]->job_mobil_no; ?><br />
						<small>( <?=$data[$i]->driver_name; ?> )</small>
					</td>

					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y H:i",strtotime($data[$i]->job_date. " ".$data[$i]->job_time)) ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->job_from; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->job_to; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->customer_company_name; ?>	
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->job_items; ?>
					</td>
					<td valign="top" style="text-align:center;"><small>
						<?=$data[$i]->job_dimensi_p; ?>cm x <?=$data[$i]->job_dimensi_l;?>cm x <?=$data[$i]->job_dimensi_t; ?>cm <br />
						( <?=$data[$i]->job_weight; ?> kg ) </small>
					</td>
					<?php if ($this->sess->user_group == 0) { ?>
						<td valign="top" style="text-align:center;">
							<a href="<?=base_url();?>andalas_job_schedule/edit_job/<?=$data[$i]->job_id;?>"> <img src="<?=base_url();?>assets/newfarrasindo/images/icon-edit.png" border="0" width="18" alt="Edit Data" title="Edit Data">
							</a>
							
							<a href="#" onclick="javascript:delete_data(<?=$data[$i]->job_id;?>)"><img src="<?=base_url();?>assets/newfarrasindo/images/icon-delete.png" border="0" width="18" alt="Delete Data" title="Delete Data"></a>
							<a href="#" onclick="javascript:set_delivered(<?=$data[$i]->job_id;?>)"><img src="<?=base_url();?>assets/transporter/images/delivered.png" border="0" width="18" alt="Set to Delivered" title="Set to Delivered"></a>
						</td>
					<?php } ?>
				</tr>
				<tr>
					<td colspan="14">
						<small><?php echo "NOTES :"." ".$data[$i]->job_notes; ?></small>
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
