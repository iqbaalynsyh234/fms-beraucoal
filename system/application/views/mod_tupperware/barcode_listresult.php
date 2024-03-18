<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
		<tr>
			<th width="2%" style="text-align:center;">No.</td>
			<th style="text-align:center;">Barcode</th>
			<th style="text-align:center;">Date</th>
			<th style="text-align:center;">Time</th>
			<th style="text-align:center;">WH</th>
			<th style="text-align:center;">DB Type</th>					
			<th style="text-align:center;">Destination</th>					
			<th style="text-align:center;">SLCARS</th>					
			<th style="text-align:center;">Expedition</th>					
			<th style="text-align:center;">Fleet Type</th>					
			<th style="text-align:center;">Fleet CBM</th>					
			<th style="text-align:center;">Control</th>
		</tr>
	</thead>
	<tbody>
	<?php
		if(count($data) > 0)
		{
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset;?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode;?></td>
					<td valign="top"><?=date("d/m/Y",strtotime($data[$i]->transporter_barcode_schedule_date));?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_time?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_wh?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_db_type?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_destination?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_slcars?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_expedition_name?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_fleet_type?></td>
					<td valign="top"><?=$data[$i]->transporter_barcode_fleet_cbm?></td>
					<td valign="top">
						<a href="javascript: delete_data('<?php echo $data[$i]->transporter_barcode_id;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete" /></a>
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
							<td colspan="12"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
