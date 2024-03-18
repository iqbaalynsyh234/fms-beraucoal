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
										<th style="text-align:center;" width="5%">Vehicle No</th>
										<th style="text-align:center;" width="5%">Vehicle Name</th>
										<th style="text-align:center;" width="7%">Alert Type</th>
										<th style="text-align:center;" width="7%">Alert Time</th>
										<th style="text-align:center;" width="20%">Location</th>
										<th style="text-align:center;" width="7%">Coordinate</th>
									</tr>
								</thead>
								<tbody>
								
								
	 <?php
		if(isset($data) && (count($data) > 0)){
			for ($i=0;$i<count($data);$i++)
				{ 
				
				?>
				<tr>
					<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $vehicle->vehicle_no;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $vehicle->vehicle_name;?></td>
					<td style="text-align:center;font-size:12px;">
						<?php if($data[$i]->gps_alert == "BO010" || $data[$i]->gps_alert == "dt" ) { ?>
							<?php echo "POWER OFF";?>
						<?php } else { ?>
							<?php echo $data[$i]->gps_alert;?>
						<?php } ?>
					</td>
					<td style="text-align:center;font-size:12px;"><?php echo date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($data[$i]->gps_time)));?></td>
					<td style="text-align:center;font-size:12px;">
						<?php 
							$location = $this->gpsmodel->GeoReverse($data[$i]->gps_latitude_real, $data[$i]->gps_longitude_real);
							$geofence = $this->gpsmodel->getGeofence_location($data[$i]->gps_longitude, $data[$i]->gps_ew, $data[$i]->gps_latitude, $data[$i]->gps_ns, $vehicle->vehicle_user_id);
						?>
						<font color="red"><?php echo $geofence; ?></font><br />
						<?php echo $location->display_name; ?>
					</td>
					<td style="text-align:center;font-size:12px;">
						<a target="_blank" href="http://maps.google.com/maps?q=<?=$data[$i]->gps_latitude_real.",".$data[$i]->gps_longitude_real;?>">
						<strong><?=$data[$i]->gps_latitude_real.",".$data[$i]->gps_longitude_real;?></strong>
						</a>
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
							</table>
							</div>
						</div>	
					
					<?php } ?>
					
					</div>
				</div>
		</div>
	</div>
</div>
