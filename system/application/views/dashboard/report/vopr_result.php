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
										<th style="text-align:center;" width="4%">Engine</th>
										<th style="text-align:center;" width="7%">Duration</th>
										<th style="text-align:center;" width="7%">Minute</th>
										<!--<th style="text-align:center;" width="10%">Location</th>
										<th style="text-align:center;" width="10%">Geofence</th>
										<th style="text-align:center;" width="5%">Coordinate</th>-->
										<th style="text-align:center;" width="10%">Location Start</th>
										<th style="text-align:center;" width="10%">Geofence Start</th>
										<th style="text-align:center;" width="5%">Coordinate Start</th>
										<th style="text-align:center;" width="10%">Location End</th>
										<th style="text-align:center;" width="10%">Geofence End</th>
										<th style="text-align:center;" width="5%">Coordinate End</th>
										<th style="text-align:center;" width="5%">Status</th>
										<th style="text-align:center;" width="5%">KM</th>
										<th style="text-align:center;" width="5%">Arah</th>
										<!--<th style="text-align:center;" width="10%">Speed Avg </th>-->
										<th style="text-align:center;" width="10%">Speed Avg</th>
										<!--<th style="text-align:center;" width="10%">Speed Avg Value</th>-->
										<!--<th style="text-align:center;" width="10%">Speed Avg Value 2</th>-->
										<!--<th style="text-align:center;" width="10%">Compare</th>-->
										<!--<th style="text-align:center;" width="10%"></th>-->
										<!--<th style="text-align:center;" width="7%">Trip Mileage</th>-->	
										<!--<th style="text-align:center;" width="7%">Commulative Mileage</th>-->
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
					
					$doorstart_status = "";
					$doorend_status = "";
					$vdoorstatus = "";
					$vtype = "";
					
					$compare_value = round($data[$i]->trip_mileage_speed_avg - $data[$i]->trip_mileage_speed_avg_rev,2);
					$compare_fix = str_replace(".", ",", $compare_value);

					
				?>
				<tr>
					<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
					<!--<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_id;?></td>-->
					<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y",strtotime($data[$i]->trip_mileage_start_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s",strtotime($data[$i]->trip_mileage_start_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y",strtotime($data[$i]->trip_mileage_end_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s",strtotime($data[$i]->trip_mileage_end_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_vehicle_no;?></td>
					<td style="text-align:center;font-size:12px;">
						<?php if($data[$i]->trip_mileage_engine == 0) { 
							$status_name = "STOP";
							$km_name = 0;
							$total_name = 0;
						?>
							<?php echo "OFF";?>
						<?php } else {
							$status_name = $data[$i]->trip_mileage_status;
							//$km_name = $data[$i]->trip_mileage_km;
							
							if($data[$i]->trip_mileage_km_status == 2){
								$km_name = str_replace(".", ",", $data[$i]->trip_mileage_km_new);
							}else{
								$km_name = str_replace(".", ",", $data[$i]->trip_mileage_km);
							}
							
							$total_name = $data[$i]->trip_mileage_totalgps;		

							if($status_name == "IDLE2" || $status_name == "IDLE"){
								
								$km_name = str_replace(".", ",",0);
								$spd_avg2 = "0";
							}else{
								$spd_avg2 = $data[$i]->trip_mileage_speed_status_rev;
								
							}
						?>
							<?php echo "ON";?>
						<?php } ?>
					</td>
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
						<br />
						<?php 
						//cek apakah sudah pernah ada filenya
						$this->db->order_by("vehicle_id","asc");
						$this->db->select("vehicle_device,vehicle_type");
						$this->db->where("vehicle_device",$data[$i]->trip_mileage_vehicle_id);
						$this->db->where("vehicle_status <>",3);
						$this->db->limit(1);
						$qv = $this->db->get("vehicle");
						$rv = $qv->row();
						if(count($rv) > 0){
							$vtype = $rv->vehicle_type;
							if($vtype == "T5DOOR"){
								$vdoorstatus = "YES";
							}else{
								$vdoorstatus = "NO";
							}
						}else{
							$vtype = "";
						}
						?>
						<!-- cek door status start-->
						<?php if(isset($data[$i]->trip_mileage_door_start) && ($data[$i]->trip_mileage_door_start == 1)){
							$doorstart_status = "OPEN";
						}
							if(isset($data[$i]->trip_mileage_door_start) && ($data[$i]->trip_mileage_door_start == 0)){
							$doorstart_status = "CLOSE";
						}
						?>
						<?php if($vdoorstatus == "YES"){ ?>
							Door: <strong><font color="red"><?php echo $doorstart_status;?></font></strong>
						<?php } ?>
						
					</td>
					<td style="text-align:center;font-size:10px;"><?php echo $data[$i]->trip_mileage_coordinate_start;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->trip_mileage_location_end;?></td>
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
					<td style="text-align:center;font-size:10px;"><?php echo $data[$i]->trip_mileage_coordinate_end;?></td>
					<!--<td style="text-align:center;font-size:12px;"><?php 
						if($data[$i]->trip_mileage_engine == "1"){
							$jkm = round($data[$i]->trip_mileage_trip_mileage,2);	
						}else{
							$jkm = 0;	
						} ?>
						<?php echo $jkm." "."KM";?>
					</td>-->
					<!--<td style="text-align:center;font-size:12px;"><?php echo $j." "."KM";?></td>-->
					<td style="text-align:center;font-size:12px;"><?php echo $status_name;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $km_name;?></td>
					<!--<td style="text-align:center;font-size:12px;"><?php echo $total_name;?></td>-->
					<td style="text-align:center;font-size:12px;">
							<?php echo $data[$i]->trip_mileage_arah_status;?>
							<!--<br /><small><?php echo $data[$i]->trip_mileage_arah_data;?></small>-->
					</td>
					<!--<td style="text-align:center;font-size:12px;">
						<?php echo $data[$i]->trip_mileage_speed_status;?>
					</td>-->
					<td style="text-align:center;font-size:12px;">
						<font color="red"><?php echo $spd_avg2; ?></font>
					</td>
					<!--<td style="text-align:center;font-size:12px;">
						<?php echo $data[$i]->trip_mileage_speed_avg;?>
					</td>-->
					<!--<td style="text-align:center;font-size:12px;">
						<font color="red"><?php echo str_replace(".", ",", $data[$i]->trip_mileage_speed_avg_rev);?>
						</font>
					</td>-->
					<!--<td style="text-align:center;font-size:12px;">
						<?php 
							//echo $compare_fix;
							echo $data[$i]->trip_mileage_km_var;
						?>
					</td>-->
					<!--<td style="text-align:center;font-size:12px;">
						<?php 
							echo $data[$i]->trip_mileage_km_status;
						?>
					</td>-->
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
