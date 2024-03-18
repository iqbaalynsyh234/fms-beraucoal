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
										<!--<th style="text-align:center;" width="5%">ID</th>-->
										<th style="text-align:center;" width="7%">Start Date</th>
										<th style="text-align:center;" width="4%">Start Time</th>
										<th style="text-align:center;" width="7%">End Date</th>
										<th style="text-align:center;" width="5%">End Time</th>
										<th style="text-align:center;" width="5%">Vehicle No</th>
										<th style="text-align:center;" width="4%">Status</th>
										<th style="text-align:center;" width="7%">Duration</th>
										<th style="text-align:center;" width="7%">Minute</th>
										<th style="text-align:center;" width="10%">Location Start</th>
										<th style="text-align:center;" width="10%">Geofence Start</th>
										<th style="text-align:center;" width="5%">Coordinate Start</th>
										<th style="text-align:center;" width="10%">Location End</th>
										<th style="text-align:center;" width="10%">Geofence End</th>
										<th style="text-align:center;" width="5%">Coordinate End</th>
										<th style="text-align:center;" width="7%">Distance(KM)</th>
										<th style="text-align:center;" width="7%">Mileage Start(KM)</th>
										<th style="text-align:center;" width="7%">Mileage End(KM)</th>
										<th style="text-align:center;" width="7%">Fuel Start(L)</th>
										<th style="text-align:center;" width="7%">Fuel End(L)</th>
										<th style="text-align:center;" width="7%">Fuel Selisih</th>
										<th style="text-align:center;" width="5%">Fuel Status</th>
									</tr>
								</thead>
								<tbody>
								
								
	 <?php
		if(isset($data) && (count($data) > 0)){
			$j = 0;
				$jkm = 0;
				for ($i=0;$i<count($data);$i++)
				{ 
					if($data[$i]->trip_mileage_engine == "1"){
						$jkm = $data[$i]->trip_mileage_trip_mileage;	
					}else{
						$jkm = 0;	
					}
					
					$j = $j + $jkm;	
					
					if($data[$i]->trip_mileage_engine == 1) { 
						$enginestatus = "MOVE";
					} else if($data[$i]->trip_mileage_engine == 2){
						$enginestatus = "IDLE";
					} else if($data[$i]->trip_mileage_engine == 3){
						$enginestatus = "OFF";
					}else{
						$enginestatus = "-";
					}
					
					if($data[$i]->trip_mileage_status == "OFF"){
						
						$delta_fuel = "";
						$trip_mileage_fuel_cons_start = "";
						$trip_mileage_fuel_cons_end = "";
						$delta_fuel = "";
					}else{
						
						$delta_fuel = $data[$i]->trip_mileage_fuel_cons_start - $data[$i]->trip_mileage_fuel_cons_end;
						$trip_mileage_fuel_cons_start = str_replace('.', ',', $data[$i]->trip_mileage_fuel_cons_start);
						$trip_mileage_fuel_cons_end = str_replace('.', ',', $data[$i]->trip_mileage_fuel_cons_end);
						$delta_fuel = str_replace('.', ',', $delta_fuel);
					}
					
					$trip_mileage_km = str_replace('.', ',', $data[$i]->trip_mileage_km);
					
					$odo_start = round(($data[$i]->trip_mileage_odo_start/1000),0, PHP_ROUND_HALF_DOWN);
					$odo_end = round(($data[$i]->trip_mileage_odo_end/1000),0, PHP_ROUND_HALF_DOWN);
					
				?>
				<tr>
					<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
					<!--<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_id;?></td>-->
					<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y",strtotime($data[$i]->trip_mileage_start_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s",strtotime($data[$i]->trip_mileage_start_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y",strtotime($data[$i]->trip_mileage_end_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s",strtotime($data[$i]->trip_mileage_end_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_vehicle_no;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $enginestatus; ?></td>
					<td style="text-align:center;font-size:12px;">
					<?php 
						if( (isset($data[$i]->trip_mileage_coordinate_list)) && ($data[$i]->trip_mileage_engine == "1") && ($data[$i]->trip_mileage_coordinate_list != "") ){ ?>
							<!--<a href="javascript:mn_map(<?=$data[$i]->trip_mileage_id;?>)" target="_blank"><?php echo $data[$i]->trip_mileage_duration;?></a> -->
							<a href="<?php echo base_url();?>operational_report/map/<?=$data[$i]->trip_mileage_id;?>" target="_blank">
							<strong><?php echo $data[$i]->trip_mileage_duration;?></strong>
							</a> 
						<?php }else{?>
							<?php echo $data[$i]->trip_mileage_duration;?>
						<?php }
					?>
					</td>
					<td style="text-align:center;font-size:12px;"><?php echo round(($data[$i]->trip_mileage_duration_sec/60),0, PHP_ROUND_HALF_DOWN);?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_location_start;?></td>
					
					<td style="text-align:center;font-size:12px;">
						<?php $geofence_start = strlen($data[$i]->trip_mileage_geofence_start); 
							if (strlen($geofence_start == 1)){	
							$geofence_start_name = "";?>
								<strong><font color="red"><?php echo $geofence_start_name." ";?></font></strong>
								
						<?php } ?>
						
						<?php
							if (strlen($geofence_start > 1)){	
							$geofence_start_name = $data[$i]->trip_mileage_geofence_start;?>
								<strong><font color="red"><?php echo $geofence_start_name." ";?></font></strong>
								
						<?php } ?>
						
						
					</td>
					<td style="text-align:center;font-size:10px;"><?php echo $data[$i]->trip_mileage_coordinate_start;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_location_end;?></td>
					<td style="text-align:center;font-size:10px;"><?php echo $data[$i]->trip_mileage_coordinate_end;?></td>
					<td style="text-align:center;font-size:12px;">
						<?php $geofence_end = strlen($data[$i]->trip_mileage_geofence_end); 
							if (strlen($geofence_end == 1)){	
							$geofence_end_name = "";?>
								<strong><font color="red"><?php echo $geofence_end_name." ";?></font></strong>
							
						<?php } ?>
						
						<?php
							if (strlen($geofence_end > 1)){	
							$geofence_end_name = $data[$i]->trip_mileage_geofence_end;?>
								<strong><font color="red"><?php echo $geofence_end_name." ";?></font></strong>
								
						<?php } ?>
						
						
					</td>
					<td style="text-align:center;font-size:12px;"><?php echo $trip_mileage_km;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $odo_start;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $odo_end;?></td>
					<td style="text-align:center;font-size:10px;"><?php echo $trip_mileage_fuel_cons_start;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $trip_mileage_fuel_cons_end;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $delta_fuel;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_status;?></td>
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
								
							</table>
							</div>
						</div>	
					
					<?php } ?>
					
					</div>
				</div>
		</div>
	</div>
</div>
