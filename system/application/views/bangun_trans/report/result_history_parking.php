<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</td>
			<th style="text-align:center;" width="10%">Vehicle</th>
			<th style="text-align:center;" width="7%">Start Time</td>
			<th style="text-align:center;" width="7%">End Time</th>
			<th style="text-align:center;" width="4%">Engine</th>			
			<th style="text-align:center;" width="20%">Location Start</th>
			<th style="text-align:center;" width="20%">Location End</th>
			<th style="text-align:center;" width="10%">Duration</th>
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
					<td><?php echo $data[$i]->parking_vehicle_name?><br /><?php echo $data[$i]->parking_vehicle_no;?></td>
					<td><?php echo $data[$i]->parking_start_time;?></td>
					<td><?php echo $data[$i]->parking_end_time;?></td>
					<td style="text-align:center;" >
						<?php if($data[$i]->parking_engine == 0) { ?>
							<?php echo "OFF";?>
						<?php } else { ?>
							<?php echo "ON";?>
						<?php } ?>
					</td>
					<td>
						<?php $geofence_start = strlen($data[$i]->parking_geofence_start); 
							if (strlen($geofence_start == 1)){	
							$geofence_start_name = "";?>
								<font color="red"><?php echo $geofence_start_name;?></font><br />
								<?php echo $data[$i]->parking_location_start;?>
						<?php } ?>
						
						<?php
							if (strlen($geofence_start > 1)){	
							$geofence_start_name = strtoupper($data[$i]->parking_geofence_start);?>
								<font color="red"><?php echo $geofence_start_name;?></font><br />
								<?php echo $data[$i]->parking_location_start;?>
						<?php } ?>
					</td>
					
					<td>
						<?php $geofence_end = strlen($data[$i]->parking_geofence_end); 
							if (strlen($geofence_end == 1)){	
							$geofence_end_name = "";?>
								<font color="red"><?php echo $geofence_end_name;?></font><br />
								<?php echo $data[$i]->parking_location_end;?>
						<?php } ?>
						
						<?php
							if (strlen($geofence_end > 1)){	
							$geofence_end_name = strtoupper($data[$i]->parking_geofence_end);?>
								<font color="red"><?php echo $geofence_end_name;?></font><br />
								<?php echo $data[$i]->parking_location_end;?>
						<?php } ?>
					</td>
					<td><?php echo $data[$i]->parking_duration;?></td>
				</tr>
		<?php
				}
			}else{
				echo "<tr><td colspan='12'>No Data Available</td></tr>";
			}
			?>
    </tbody>
		<tr>
		<td colspan="6"><strong>Total Duration</strong></td>
		<td colspan="3" style="text-align:center;"><strong><?php 
			if (isset($totalduration))
									{
										$conval = $totalduration;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												echo $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												echo $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												echo $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												echo $minutes." "."Minutes"." ";
											}
										}
										/*  if(isset($seconds) && $seconds > 0)
										{
											echo $seconds." "."Detik"." ";
										} */
									}
									
									?></strong>
			
			</td>
		</tr>
	
</table>