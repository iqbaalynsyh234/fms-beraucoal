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

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading panel-heading-blue">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($devicealert) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel" style="overflow-y:auto;">
                <table style="font-size: 12px;" id="example4" class="table table-striped table-bordered table-hover full-width">
                  <thead>
                      <tr>
                        <th>No</th>
												<th>Date</th>
                        <th>Time</th>
                        <th>Vehicle No</th>
												<th>Vehicle Name</th>
                        <th>Alert Name</th>
												<th>Position</th>
												<th>Coordinate</th>
												<!-- <th>GPS Status</th> -->
												<th>GPS Speed (Kph)</th>
												<th>Area</th>
												<th>Speed Limit (Kph)</th>
												<th>Jalur</th>
                      </tr>
                  </thead>
                  <tbody>
										<?php $devicealertarray = array(
											"dt"    => "Cut Power Alert",
											"BO010" => "Cut Power Alert",
											"BO012" => "Panic Button (SOS)"
										);
										 ?>
                    <?php $no = 1; foreach ($devicealert as $rowalert) {?>
                      <tr>
                        <td style="text-align:center;"><?php echo $no; ?></td>
												<td style="text-align:center;" width="10%">
													<?php echo date("d-m-Y", strtotime($rowalert['vehicle_alert_datetime'])) ?>
												</td>

												<td style="text-align:center;" width="10%">
													<?php echo date("H:i:s", strtotime($rowalert['vehicle_alert_datetime'])) ?>
												</td>
                        <td style="text-align:center;" width="10%"><?php echo $rowalert['vehicle_no'] ; ?></td>
												<td style="text-align:center;" width="10%"><?php echo $rowalert['vehicle_name']; ?></td>
                        <td style="text-align:center; color:red;" width="15%">
													<?php
													 	if (in_array($rowalert['gps_alert'], $devicealertarray)) {
													 		echo $devicealertarray[$rowalert['gps_alert']]."<br>";
													 	}else {
													 		echo $rowalert['gps_alert']."<br>";
													 	}
													 ?>
												</td>

												<td style="text-align:center;">
													<?php
													$addresxplode = explode(",", $rowalert['address']);
														echo $addresxplode[0];
													?>
												</td>

												<td style="text-align:center;">
													<a href="https://maps.google.com/?q=<?php echo $rowalert['vehicle_lat'].','.$rowalert['vehicle_lng'] ?>" target="_blank">
														<?php echo $rowalert['vehicle_lat'].','.$rowalert['vehicle_lng'] ?>
													</a>
												</td>

												<!-- <td style="text-align:center;">
													<?php
														if ($rowalert['gps_status'] == "A" || $rowalert['gps_status'] == "V") {
															echo "OK"."<br>";
														}else {
															echo "NOT OK"."<br>";
														}
													 ?>
												</td> -->

												<td style="color:red; text-align:center;" width="8%">
													<?php echo number_format($rowalert['gps_speed']*1.852, 0, "",".")."</br>" ?>
												</td>

												<td width="10%">
													<?php echo $rowalert['gps_geofence']?>
												</td>

												<td style="color:red; text-align:center;" width="8%">
													<?php echo $rowalert['gps_speed_limit']?>
												</td>

												<td style="text-align:center;" width="10%">
													<?php echo $rowalert['gps_last_road_type'];  ?>
												</td>

                      </tr>
                    <?php $no++; } ?>
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

<!-- <?php
  function getaddress($lat,$lng){
		 $key  		= "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";
     $url 		= 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.$key;
		 // echo "url : ".$url.'<br>';
		 $json 		= @file_get_contents($url);
     $data		=json_decode($json);
     $status  = $data->status;
     if($status=="OK")
     {
       return $data->results[0]->formatted_address;
     }
     else
     {
       return false;
     }
  }
?> -->
