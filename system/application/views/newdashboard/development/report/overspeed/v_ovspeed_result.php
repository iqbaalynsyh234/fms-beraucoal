<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
				// window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
				var company           = jQuery("#company").val();
				var vehicle           = jQuery("#vehicle").val();
				var startdate         = jQuery("#startdate").val();
				var enddate           = jQuery("#enddate").val();
				var shour             = jQuery("#shour").val();
				var ehour             = jQuery("#ehour").val();
				var jalur             = jQuery("#jalur").val();
				var geofence          = jQuery("#geofence").val();
				var periode           = jQuery("#periode").val();
				var km_checkbox       = jQuery("#km_checkbox").val();
				var kmselected_select = jQuery("#kmselected_select").val();
				var kmstart           = jQuery("#kmstart").val();
				var kmend             = jQuery("#kmend").val();

				var data = {
					company : company,
					vehicle : vehicle,
					startdate : startdate,
					enddate : enddate,
					shour : shour,
					ehour : ehour,
					jalur : jalur,
					geofence : geofence,
					periode : periode,
					km_checkbox : km_checkbox,
					kmselected_select : kmselected_select,
					kmstart : kmstart,
					kmend : kmend
				};

				console.log("data for sent : ", data);
				jQuery("#loader2").show();
				jQuery.post("<?php echo base_url() ?>development/search_overspeedreportphpexcel", data, function(response){
					console.log("response result : ", response);
					jQuery("#loader2").hide();
					var $a = $("<a>");
					$a.attr("href",response.file);
					$("body").append($a);
					$a.attr("download","Overspeed_report.xls");
					$a[0].click();
					$a.remove();
					// window.open('<?php echo base_url() ?>development/search_overspeedreportphpexcel','_blank');
				}, "json");
			});
		}
	);

	// var display = $("#storage_data").data("storage");
	// var data = {
	// 	dataforexport : display
	// };
	// jQuery.post("<?php echo base_url() ?>development/search_overspeedreportphpexcel", data, function(response){
	// 	jQuery("#loader2").hide();
	// 	console.log("response result : ", response);
	// 	var $a = $("<a>");
	// 	$a.attr("href",response.file);
	// 	$("body").append($a);
	// 	$a.attr("download","Overspeed_report.xls");
	// 	$a[0].click();
	// 	$a.remove();
	// }, "json");


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
				<td style="text-align:center;"><small>Periode: <?php echo $startdate." - ".$enddate;?></small></td>
			</tr>

			<header class="panel-heading panel-heading-red">REPORT - DEVELOPMENT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel"  style="overflow-y:auto;">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="3%">No</th>
										<th style="text-align:center;" width="10%">Driver</th>
										<th style="text-align:center;" width="10%">Date</th>
										<th style="text-align:center;" width="10%">Time</th>
										<th style="text-align:center;" width="10%">Shift</th>
										<th style="text-align:center;" width="10%">Vehicle No</th>
										<th style="text-align:center;" width="10%">Vehicle Name</th>
										<th style="text-align:center;" width="7%">Alarm Name</th>
										<th style="text-align:center;" width="8%">Position</th>
										<th style="text-align:center;" width="8%">Lat</th>
										<th style="text-align:center;" width="8%">Long</th>
										<th style="text-align:center;" width="10%">Geofence</th>
										<th style="text-align:center;" width="5%">GPS Speed(kph)</th>
										<th style="text-align:center;" width="7%">Speed Limit(kph)</th>
										<th style="text-align:center;" width="7%">Selisih(kph)</th>
										<th style="text-align:center;" width="7%">Jalur</th>
									</tr>
								</thead>
								<tbody>


	 <?php
		if(isset($data) && (count($data) > 0)){

				for ($i=0;$i<count($data);$i++)
				{
					$selisih_speed = ($data[$i]->overspeed_report_speed - $data[$i]->overspeed_report_geofence_limit);

					if ($selisih_speed > 6) {?>
						<tr>
							<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
							<td style="text-align:center;font-size:12px;"></td>
							<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y", strtotime($data[$i]->overspeed_report_gps_time));?></td>
							<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s", strtotime($data[$i]->overspeed_report_gps_time));?></td>
							<td style="text-align:center;font-size:12px;">
								<?php
								$shiftfix = "-";
									$timeforshift = date("H:i:s", strtotime($data[$i]->overspeed_report_gps_time));
										if ($timeforshift >= "06:00:00" && $timeforshift <= "17:59:59") {
											$shiftfix = 1;
										}else {
											$shiftfix = 2;
										}
								 ?>
								<?php echo $shiftfix;?>
							</td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_vehicle_no;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_vehicle_name;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_name;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_location;?></td>
								<?php
										$coordexplode = explode(",", $data[$i]->overspeed_report_coordinate);
										$coordLat = $coordexplode[0];
										$coordLong = $coordexplode[1];
								 ?>
							<td style="text-align:center;font-size:12px;"><?php echo $coordLat;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $coordLong;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_geofence_name;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_speed;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_geofence_limit;?></td>
							<td style="text-align:center;font-size:12px;"><?php echo ($data[$i]->overspeed_report_speed - $data[$i]->overspeed_report_geofence_limit);?></td>
							<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->overspeed_report_jalur;?></td>
						</tr>
					<?php }
				?>

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
