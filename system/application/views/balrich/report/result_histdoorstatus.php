<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</td>
			<th style="text-align:center;" width="10%">Vehicle</th>
			<th style="text-align:center;" width="7%">Start Time</th>
			<th style="text-align:center;" width="9%">End Time</td>
			<th style="text-align:center;" width="9%">Door</th>
			<th style="text-align:center;" width="7%">Duration</th>
			<th style="text-align:center;" width="20%">Location Start</th>
			<th style="text-align:center;" width="20%">Location End</th>
			
			<!--<th style="text-align:center;" width="7%">Trip Mileage</th>		
			<th style="text-align:center;" width="7%">Commulative Mileage</th>-->
			
		</tr>
    </thead>
	<tbody>
		<?php
			if (count($data)>0)
			{
				for ($i=0;$i<count($data);$i++)
				{ 
				?>
				<tr>
					<td style="text-align:center;" ><?php echo $i+1;?></td>
					<td style="text-align:center;"><?php echo $data[$i]->door_vehicle_name." ".$data[$i]->door_vehicle_no;?></td>
					<td style="text-align:center;"><?php echo $data[$i]->door_start_time;?></td>
					<td style="text-align:center;"><?php echo $data[$i]->door_end_time;?></td>
					<td style="text-align:center;" >
						<?php echo $data[$i]->door_status;?>
					</td>
					<td><?php echo $data[$i]->door_duration;?></td>
					<td>
						<?php $geofence_start = strlen($data[$i]->door_geofence_start); 
							if (strlen($geofence_start == 1)){	
							$geofence_start_name = "";?>
								<strong><font color="red"><?php echo $geofence_start_name;?></font></strong><br />
								<?php echo $data[$i]->door_location_start;?><br />
						<?php } ?>
						
						<?php
							if (strlen($geofence_start > 1)){	
							$geofence_start_name = $data[$i]->door_geofence_start;?>
								<strong><font color="red"><?php echo $geofence_start_name;?></font></strong><br />
								<?php echo $data[$i]->door_location_start;?><br />
						<?php } ?>
						<strong><font color="red"><?php echo $data[$i]->door_coordinate_start;;?></font></strong>
					</td>
					
					<td>
						<?php $geofence_end = strlen($data[$i]->door_geofence_end); 
							if (strlen($geofence_end == 1)){	
							$geofence_end_name = "";?>
								<strong><font color="red"><?php echo $geofence_end_name;?></font></strong><br />
								<?php echo $data[$i]->door_location_end;?><br />
						<?php } ?>
						
						<?php
							if (strlen($geofence_end > 1)){	
							$geofence_end_name = $data[$i]->door_geofence_end;?>
								<strong><font color="red"><?php echo $geofence_end_name;?></font></strong><br />
								<?php echo $data[$i]->door_location_end;?><br />
						<?php } ?>
						<strong><font color="red"><?php echo $data[$i]->door_coordinate_end;;?></font></strong>
					</td>
					
					
				</tr>
		<?php
				}
			}else{
				echo "<tr><td colspan='12'>No Data Available</td></tr>";
			}
			?>
    </tbody>
	
</table>