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


							<div class="col-lg-6 col-sm-6">	
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">	
							</div>
							<br />
							
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
		<tr>
			<td style="text-align:center;">
				<small>Total Data GPS : <?=$totaldatagps?></small>
			</td>	
		</tr>

			<header class="panel-heading panel-heading-blue">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">	
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">
							
							<div class="col-lg-4 col-sm-4">	
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>	
							
							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="3%">No</th>
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
		if(isset($data) && (count($data) > 0)){
		for ($i=0;$i<count($data);$i++)
			{ 
				?>
				<tr>
					<td style="text-align:center;font-size:12px;" ><?php echo $i+1;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->pto_vehicle_name." ".$data[$i]->pto_vehicle_no;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->pto_start_time;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->pto_end_time;?></td>
					<td style="text-align:center;font-size:12px;" >
						<?php echo $data[$i]->pto_status;?>
					</td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->pto_duration;?><br /><small><?php echo $data[$i]->pto_duration_sec;?> s</small></td>
					<td style="text-align:center;font-size:12px;">
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
					
					<td style="text-align:center;font-size:12px;">
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
	?>
        <tr>
        	<td colspan="10">No Available Data</td>
		</tr>
	<?php
		}
	?>
								</tbody>
								<tr>
									<td colspan="7" style="text-align:center;font-size:12px;"><strong>Total Duration ON</strong></td>
									<td colspan="3" style="text-align:center;font-size:12px;"><strong><?php 
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
						</div>	
					
					<?php } ?>
					
					</div>
				</div>
		</div>
	</div>
</div>
