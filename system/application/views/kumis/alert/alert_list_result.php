		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="10%" valign="top" style="text-align:center;">Vehicle</th>
					<th width="8%" valign="top" style="text-align:center;">Datetime</th>
					<th width="8%" valign="top" style="text-align:center;">View Status</th>
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
						<?php 
							if (isset($row_vehicle))
							{
								foreach ($row_vehicle as $row_vehicles)
								{
									if ($row_vehicles->vehicle_device == $data[$i]->alert_vehicle_device)
									{
										echo $row_vehicles->vehicle_no;
									}
								}
							}
						?><br />
						<?php 
							if (isset($row_vehicle))
							{
								foreach ($row_vehicle as $row_vehicles)
								{
									if ($row_vehicles->vehicle_device == $data[$i]->alert_vehicle_device)
									{
										echo $row_vehicles->vehicle_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y H:i",strtotime($data[$i]->alert_datetime)); ?>
					</td>
					<td valign="center" style="text-align:center;">
					<?php if ($data[$i]->alert_view_status == 1) { ?>
						<img src="<?=base_url();?>assets/newfarrasindo/images/icon-yes.png" height="20" width="20" width="20" title="seen" border="0">
					<?php } ?>
					<?php if ($data[$i]->alert_view_status == 0) { ?>
						<img src="<?=base_url();?>assets/newfarrasindo/images/icon-no.png" height="20" width="20" border="0">
					<?php } ?>
					</td>
					
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='10'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
						<td colspan="10"><!--<?=$paging?>--></td>
					</tr>
			</tfoot>
		</table>
