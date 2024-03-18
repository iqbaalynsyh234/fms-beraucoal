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
										<th style="text-align:center;" width="10%">Vehicle</th>
										<th style="text-align:center;" width="10%">GPS Date</th>
										<th style="text-align:center;" width="7%">GPS Time</th>
										<th style="text-align:center;" width="7%">GPS Speed (kph)</th>
										<th style="text-align:center;" width="7%">Status</th>
										<th style="text-align:center;" width="10%">Location</th>
										<th style="text-align:center;" width="4%">Jalur</th>
										<th style="text-align:center;" width="5%">Coordinate</th>
										<th style="text-align:center;" width="5%">Odometer (KM)</th>
										<th style="text-align:center;" width="7%">Fuel (L)</th>
										<th style="text-align:center;" width="7%">Fuel (%)</th>

									</tr>
								</thead>
								<tbody>


	 <?php
		if(isset($data) && (count($data) > 0)){
			$j = 0;
				$jkm = 0;
				$totalliterbensin = 0;
				for ($i=0;$i<count($data);$i++)
				{
					$ad1_volt = $data[$i]->location_report_fuel_data;

					//ultrasonic fuel
					$fullcap         = $vehicle_fuel_capacity;//200; // liter
					$fullpercent     = 100; // percentage
					$fullvolt		     = $vehicle_fuel_volt;//3.54;//$maxvoltage;
					$currentvolt     = $ad1_volt;

					$percenvoltase   = $currentvolt * ($fullpercent / $fullvolt); // persentase yg didapat dari perubahan voltase;
					$sisaliterbensin = ($percenvoltase * $fullcap) / $fullpercent;

					$percenvoltase   = str_replace('.', ',', $percenvoltase);
					$sisaliterbensin = str_replace('.', ',', $sisaliterbensin);
					$locationname    = str_replace('KM', '', $data[$i]->location_report_location);
					$totalliterbensin = $totalliterbensin + $sisaliterbensin;
				?>
				<!-- <?php echo $maxvoltage; ?> -->
				<tr>
					<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->location_report_vehicle_no;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y",strtotime($data[$i]->location_report_gps_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s",strtotime($data[$i]->location_report_gps_time));?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->location_report_speed;?></td>
					<td style="text-align:center;font-size:12px;">
						<?php
							if($data[$i]->location_report_name == "location_idle"){
								$status_name = "IDLE";
							}else if($data[$i]->location_report_name == "location_off"){
								$status_name = "OFF";
							}else{
								$status_name = "MOVE";
							}
						?>
						<?php echo $status_name; ?>
					</td>
					<td style="text-align:center;font-size:12px;"><?php echo $locationname;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->location_report_jalur;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->location_report_coordinate;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo round(($data[$i]->location_report_odometer/1000),0, PHP_ROUND_HALF_DOWN);?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $sisaliterbensin;?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $percenvoltase;?></td>
				</tr>
		<?php }?>
			<!-- <tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>Total Ltr</td>
				<td><?php echo $totalliterbensin; ?></td>
				<td></td>
			</tr> -->
		<?php }else{ ?>
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
