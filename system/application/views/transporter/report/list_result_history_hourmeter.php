<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<thead>
	<tr>
		<th width="3%" style="text-align:center">No</td>
		<th width="8%" style="text-align:center">Vehicle No</th>
		<th width="10%" style="text-align:center">Vehicle Name</th>
		<th width="3%" style="text-align:center">Active</td>
		<th width="10%" style="text-align:center">Start Working Time</td>
		<th width="10%" style="text-align:center">End Working Time</th>					
		<th width="7%" style="text-align:center">Duration</th>
		<th width="7%" style="text-align:center">Cumulative</th>
		<th width="15%" style="text-align:center">Project Location</th>
	</tr>
</thead>
<tbody>
	<?php 
		if (isset($data))
		{
			for ($i=0;$i<count($data);$i++)
			{
		?>
			<tr>
				<td><?php echo $i+1;?></td>
				<td><?php echo $data[$i]->report_hourmeter_vehicle_no;?></td>
				<td><?php echo $data[$i]->report_hourmeter_name;?></td>
				<td><?php echo $data[$i]->report_hourmeter_active;?></td>
				<td><?php echo $data[$i]->report_hourmeter_start;?></td>
				<td><?php echo $data[$i]->report_hourmeter_end;?></td>
				<td><?php echo $data[$i]->report_hourmeter_duration;?></td>
				<td><?php echo $data[$i]->report_hourmeter_cumulative;?></td>
				<td><?php echo $data[$i]->report_hourmeter_location;?></td>
			</tr>
			<?php
			if (isset($data[$i+1]->report_hourmeter_start))
			{
				$y = explode(" ", $data[$i]->report_hourmeter_start);
				$z = explode(" ", $data[$i+1]->report_hourmeter_start);
				if ($y[0] != $z[0])
				{
			?>
				<tr><td colspan="9"><hr /></td></tr>
			<?php
				}
			}
			?>
	<?php
			}
		}
		else
		{
	?>
		<tr>
			<td colspan="9">Data Not Available</td>
		</tr>
	<?php
		}
	?>
</tbody>
</table>