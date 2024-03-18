<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<thead>
	<tr>
		<th width="2%">No.</td>
		<th style="text-align:center;">Timesheet</th>
		<th style="text-align:center;" >Timeplan ( In )</th>
		<th style="text-align:center;" >Timeplan ( Out )</th>
		<th style="text-align:center;" >Route</th>
		<th style="text-align:center;" >Vehicle</th>
		<th style="text-align:center;" >Driver</th>
		<th style="text-align:center;" >Cycle</th>
		<th style="text-align:center;">Control</th>
	</tr>
</thead>
<tbody>
	<?php 
		for($i=0;$i<count($data);$i++)
		{
	?>
	<tr>
		<td><?=$i+1+$offset?></td>
		<td><?php echo $data[$i]->timesheet_geo_name; ?></td>
		<td><?php echo date("H:i",strtotime($data[$i]->timesheet_time)); ?></td>
		<td><?php echo date("H:i",strtotime($data[$i]->timesheet_time_out)); ?></td>
		<td>
			<?php 
				if (isset($route)&&count($route)>0)
				{
					foreach($route as $myroute)
					{
						if($myroute->route_id == $data[$i]->timesheet_route)
						{
							echo $myroute->route_name;
						}
					}
				}
			?>
		</td>
		<td>
			<?php 
				if (isset($vehicle) && count($vehicle)>0)
				{
					foreach($vehicle as $myvehicle)
					{
						if($myvehicle->vehicle_device == $data[$i]->timesheet_vehicle)
						{
							echo $myvehicle->vehicle_name." ".$myvehicle->vehicle_no;
						}
					}
				}
			?>
		</td>
		<td>
			<?php 
				if (isset($driver) && count($driver)>0)
				{
					foreach($driver as $mydriver)
					{
						if($mydriver->driver_id == $data[$i]->timesheet_driver)
						{
							echo $mydriver->driver_name;
						}
					}
				}
			?>
		</td>
		<td>
			<?php 
				if (isset($data[$i]->timesheet_cycle) && $data[$i]->timesheet_cycle != 0)
				{
					echo $data[$i]->timesheet_cycle; 
				}
			?>
		</td>
		<td>
		<a href="javascript: timesheet_edit('<?php echo $data[$i]->timesheet_id;?>')" title="Edit"><img src="<?php echo base_url();?>assets/images/edit.gif" /></a>
		<a href="javascript: timesheet_delete('<?php echo $data[$i]->timesheet_id;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete" /></a>
		</td>
	</tr>
	<?php } ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="9"><?php if (isset($paging)) { echo $paging; } ?></td>
	</tr>
</tfoot>
</table>
