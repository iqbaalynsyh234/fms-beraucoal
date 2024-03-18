<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);
</script>
<br />
<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a> 
<p>
<h4>Total Data GPS : <?=$totaldatagps?></h4> <br/>
<div id="isexport_xcel">
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</td>
			<th style="text-align:center;" width="10%">Vehicle</th>
			<th style="text-align:center;" width="9%">Start Time</th>
			<th style="text-align:center;" width="9%">End Time</td>
			<th style="text-align:center;" width="7%">PTO</th>
			<th style="text-align:center;" width="7%">Duration</th>
			<th style="text-align:center;" width="20%">Location Start</th>
			<th style="text-align:center;" width="20%">Location End</th>
			
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
					<td style="text-align:center;"><?php echo $data[$i]->pto_vehicle_name." ".$data[$i]->pto_vehicle_no;?></td>
					<td style="text-align:center;"><?php echo $data[$i]->pto_start_time;?></td>
					<td style="text-align:center;"><?php echo $data[$i]->pto_end_time;?></td>
					<td style="text-align:center;" >
						<?php echo $data[$i]->pto_status;?>
					</td>
					<td><?php echo $data[$i]->pto_duration;?><br /><small><?php echo $data[$i]->pto_duration_sec;?> s</small></td>
					<td>
						<?php $geofence_start = strlen($data[$i]->pto_geofence_start); 
							if (strlen($geofence_start == 1)){	
							$geofence_start_name = "";?>
								<strong><font color="red"><?php echo $geofence_start_name;?></font></strong><br />
								<?php echo $data[$i]->pto_location_start;?><br />
						<?php } ?>
						
						<?php
							if (strlen($geofence_start > 1)){	
							$geofence_start_name = $data[$i]->pto_geofence_start;?>
								<strong><font color="red"><?php echo $geofence_start_name;?></font></strong><br />
								<?php echo $data[$i]->pto_location_start;?><br />
						<?php } ?>
						<strong><font color="red"><?php echo $data[$i]->pto_coordinate_start;;?></font></strong>
					</td>
					
					<td>
						<?php $geofence_end = strlen($data[$i]->pto_geofence_end); 
							if (strlen($geofence_end == 1)){	
							$geofence_end_name = "";?>
								<strong><font color="red"><?php echo $geofence_end_name;?></font></strong><br />
								<?php echo $data[$i]->pto_location_end;?><br />
						<?php } ?>
						
						<?php
							if (strlen($geofence_end > 1)){	
							$geofence_end_name = $data[$i]->pto_geofence_end;?>
								<strong><font color="red"><?php echo $geofence_end_name;?></font></strong><br />
								<?php echo $data[$i]->pto_location_end;?><br />
						<?php } ?>
						<strong><font color="red"><?php echo $data[$i]->pto_coordinate_end;;?></font></strong>
					</td>
					
				</tr>
		<?php
				}
			}else{
				echo "<tr><td colspan='13'>No Data Available</td></tr>";
			}
			?>
    </tbody>
	<tr>
		<td colspan="7"><strong>Total Duration ON</strong></td>
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
</div>