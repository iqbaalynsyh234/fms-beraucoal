		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<th style="text-align:center;">Vehicle</th>
					<th style="text-align:center;">Alert Created</th>
					<th style="text-align:center;">Delete</th>
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
					<td valign="top">
						<?php 
							if (isset($mobil)&&count($mobil)>0)
							{
								foreach ($mobil as $value)
								{
									if ($value->mobil_device == $data[$i]->service_alert_vehicle)
									{
										echo $value->mobil_name." ".$value->mobil_no;
									}
								}
							}
						?>
					</td>
					<td valign="top">
						<?php echo $data[$i]->service_alert_vehicle_create;?>
					</td>
					<td valign="top">
						<a href="javascript: service_delete_data('<?php echo $data[$i]->service_alert_id;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" /></a>
					</td>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='3'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="3"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
